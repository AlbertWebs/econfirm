<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SmsLog::query();

        if ($request->filled('status')) {
            $query->where('is_success', $request->string('status') === 'success');
        }

        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->trim().'%';
            $query->where(function ($w) use ($needle) {
                $w->where('recipient', 'like', $needle)
                    ->orWhere('correlator', 'like', $needle)
                    ->orWhere('provider_unique_id', 'like', $needle)
                    ->orWhere('provider_message', 'like', $needle);
            });
        }

        $logs = $query->orderByDesc('id')->paginate(30)->withQueryString();

        return view('admin.sms-logs.index', compact('logs'));
    }
}
