<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    private function ensureAdmin(): void
    {
        if (
            strtolower(
                session(
                    'session_access_level',
                    ''
                )
            ) !== 'admin'
        ) {
            abort(
                403,
                'Unauthorized access.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOG PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->ensureAdmin();

        $auditLogs = AuditLog::with('user')
            ->latest('created_at')
            ->get();

        return view(
            'auditlog',
            compact('auditLogs')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD AUDIT LOGS AS PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf(Request $request)
    {
        $this->ensureAdmin();

        $auditLogs = AuditLog::with('user')
            ->when(
                $request->filled('search'),
                function (
                    Builder $query
                ) use ($request) {
                    $search = trim(
                        $request->input(
                            'search'
                        )
                    );

                    $query->where(
                        function (
                            Builder $subQuery
                        ) use ($search) {
                            $subQuery
                                ->where(
                                    'actor_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'action',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'module',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'affected_record',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'ip_address',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('role'),
                function (
                    Builder $query
                ) use ($request) {
                    $query->where(
                        'user_role',
                        $request->input('role')
                    );
                }
            )
            ->when(
                $request->filled('module'),
                function (
                    Builder $query
                ) use ($request) {
                    $query->whereRaw(
                        'LOWER(module) = ?',
                        [
                            strtolower(
                                $request->input(
                                    'module'
                                )
                            ),
                        ]
                    );
                }
            )
            ->when(
                $request->filled('result'),
                function (
                    Builder $query
                ) use ($request) {
                    $query->where(
                        'result',
                        $request->input('result')
                    );
                }
            )
            ->when(
                $request->filled('date'),
                function (
                    Builder $query
                ) use ($request) {
                    $query->whereDate(
                        'created_at',
                        $request->input('date')
                    );
                }
            )
            ->latest('created_at')
            ->get();

        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'module' => $request->input('module'),
            'result' => $request->input('result'),
            'date' => $request->input('date'),
        ];

        $pdf = Pdf::loadView(
            'auditlog_pdf',
            compact(
                'auditLogs',
                'filters'
            )
        )
            ->setPaper(
                'a4',
                'landscape'
            );

        return $pdf->download(
            'audit-logs-'
            . now()->format('Y-m-d-His')
            . '.pdf'
        );
    }
}