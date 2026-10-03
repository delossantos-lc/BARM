<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditEveryTransaction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->shouldRecord($request)) {
            return $next($request);
        }

        try {
            $response = $next($request);

            [$result, $description] = $this->responseResult(
                $request,
                $response
            );

            $this->writeLog($request, $result, $description);

            return $response;
        } catch (Throwable $exception) {
            $this->writeLog(
                $request,
                'failed',
                $this->safeErrorMessage($exception)
            );

            throw $exception;
        }
    }

    private function shouldRecord(Request $request): bool
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return false;
        }

        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'audit')) {
            return false;
        }

        return true;
    }

    private function responseResult(
        Request $request,
        Response $response
    ): array {
        $statusCode = $response->getStatusCode();

        $flashError = null;

        if ($request->hasSession()) {
            $flashError = $request->session()->get('error')
                ?? $request->session()->get('fail');
        }

        if ($statusCode >= 400 || $flashError) {
            return [
                'failed',
                $flashError
                    ? (string) $flashError
                    : 'Request failed with HTTP status ' . $statusCode . '.',
            ];
        }

        $successMessage = null;

        if ($request->hasSession()) {
            $successMessage = $request->session()->get('success');
        }

        return [
            'success',
            $successMessage
                ? (string) $successMessage
                : 'Transaction completed successfully.',
        ];
    }

    private function writeLog(
        Request $request,
        string $result,
        string $description
    ): void {
        try {
            AuditLogger::record(
                $this->actionName($request),
                $this->moduleName($request),
                $this->affectedRecord($request),
                null,
                null,
                $result,
                $description,
                $request
            );
        } catch (Throwable $loggingError) {
            report($loggingError);
        }
    }

    private function actionName(Request $request): string
    {
        $routeName = trim(
            (string) optional($request->route())->getName()
        );

        if ($routeName !== '') {
            return $routeName;
        }

        return $request->method() . ' ' . $request->path();
    }

    private function moduleName(Request $request): string
    {
        $controllerAction = (string) optional($request->route())
            ->getActionName();

        if ($controllerAction === '' || $controllerAction === 'Closure') {
            return 'System';
        }

        $controller = explode('@', $controllerAction)[0];
        $controller = class_basename($controller);

        return str_replace('Controller', '', $controller) ?: 'System';
    }

    private function affectedRecord(Request $request): ?string
    {
        $parameters = optional($request->route())->parameters() ?? [];

        if (empty($parameters)) {
            return null;
        }

        $records = [];

        foreach ($parameters as $name => $value) {
            if ($value instanceof Model) {
                $value = $value->getKey();
            } elseif (is_array($value) || is_object($value)) {
                continue;
            }

            $records[] = $name . ': ' . $value;
        }

        return empty($records) ? null : implode(', ', $records);
    }

    private function safeErrorMessage(Throwable $exception): string
    {
        $message = trim($exception->getMessage());

        if ($message === '') {
            return class_basename($exception);
        }

        return mb_substr($message, 0, 1000);
    }
}
