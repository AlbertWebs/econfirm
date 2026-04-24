<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = AdminActivityLog::query()
            ->with('admin')
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.activity.index', compact('logs'));
    }
}
