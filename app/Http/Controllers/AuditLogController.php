<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::latest()->get();

        return view('admin.audit.index', compact('logs'));
    }
}
