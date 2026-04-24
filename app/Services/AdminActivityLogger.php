<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    public static function log(string $action, ?string $subjectType = null, ?int $subjectId = null, array $metadata = []): void
    {
        if (! Auth::guard('admin')->check()) {
            return;
        }

        AdminActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => $metadata === [] ? null : $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
