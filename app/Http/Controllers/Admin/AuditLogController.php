<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->acao, fn ($q) => $q->where('action', 'like', "%{$request->acao}%"))
            ->latest()
            ->paginate(30);

        return view('admin.audit-logs.index', compact('logs'));
    }
}
