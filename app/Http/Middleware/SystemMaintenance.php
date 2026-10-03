<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app()->bound('systemSettings')
            ? app('systemSettings')
            : SystemSetting::current();

        if (!$settings->maintenance_mode) {
            return $next($request);
        }

        $role = strtolower(trim((string) (
            $request->session()->get('session_access_level')
            ?? $request->session()->get('access_level')
            ?? $request->user()?->access_level
            ?? ''
        )));

        $alwaysAllowed = $request->routeIs(
            'login',
            'login.authenticate',
            'logout',
            'settings.index',
            'settings.update'
        );

        if ($role === 'admin' || $alwaysAllowed) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $settings->maintenance_message,
            ], 503);
        }

        return response()->view(
            'maintenance',
            compact('settings'),
            503
        );
    }
}
