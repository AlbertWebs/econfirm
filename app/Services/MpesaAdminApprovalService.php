<?php

namespace App\Services;

use App\Models\MpesaB2b;
use App\Models\MpesaB2c;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MpesaAdminApprovalService
{
    public function __construct(
        protected MpesaService $mpesaService
    ) {}

    /**
     * @return array{ok: bool, message: string}
     */
    public function approveB2c(MpesaB2c $record): array
    {
        if (! $record->isPending()) {
            return ['ok' => false, 'message' => 'Only pending B2C items can be approved.'];
        }

        return DB::transaction(function () use ($record) {
            $adminId = (int) Auth::guard('admin')->id();
            $record->update([
                'status' => 'Approved',
                'approved_by_admin_id' => $adminId,
                'approved_at' => now(),
                'rejected_by_admin_id' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
            $record->refresh();

            try {
                $dispatch = $this->mpesaService->submitB2cFromStoredRequest($record);
            } catch (\Throwable $e) {
                \Log::error('M-Pesa B2C admin dispatch failed', ['mpesa_b2c_id' => $record->id, 'error' => $e->getMessage()]);

                return ['ok' => true, 'message' => 'Marked approved, but dispatch raised an error. Check logs.'];
            }
            if (! $dispatch['success']) {
                return ['ok' => true, 'message' => 'Marked approved, but Safaricom dispatch failed: '.($dispatch['message'] ?? 'Unknown error')];
            }

            return ['ok' => true, 'message' => 'Approved and submitted to Safaricom.'];
        });
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function rejectB2c(MpesaB2c $record, string $reason): array
    {
        if (! $record->isPending()) {
            return ['ok' => false, 'message' => 'Only pending B2C items can be rejected.'];
        }

        $record->update([
            'status' => 'Rejected',
            'rejected_by_admin_id' => Auth::guard('admin')->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'approved_by_admin_id' => null,
            'approved_at' => null,
        ]);

        return ['ok' => true, 'message' => 'B2C request rejected.'];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function approveB2b(MpesaB2b $record): array
    {
        if (! $record->isPending()) {
            return ['ok' => false, 'message' => 'Only pending B2B items can be approved.'];
        }

        return DB::transaction(function () use ($record) {
            $adminId = (int) Auth::guard('admin')->id();
            $record->update([
                'status' => 'Approved',
                'approved_by_admin_id' => $adminId,
                'approved_at' => now(),
                'rejected_by_admin_id' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
            $record->refresh();

            try {
                $dispatch = $this->mpesaService->submitB2bFromStoredRequest($record);
            } catch (\Throwable $e) {
                \Log::error('M-Pesa B2B admin dispatch failed', ['mpesa_b2b_id' => $record->id, 'error' => $e->getMessage()]);

                return ['ok' => true, 'message' => 'Marked approved, but dispatch raised an error. Check logs.'];
            }
            if (! $dispatch['success']) {
                return ['ok' => true, 'message' => 'Marked approved, but Safaricom dispatch failed: '.($dispatch['message'] ?? 'Unknown error')];
            }

            return ['ok' => true, 'message' => 'Approved and submitted to Safaricom.'];
        });
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function rejectB2b(MpesaB2b $record, string $reason): array
    {
        if (! $record->isPending()) {
            return ['ok' => false, 'message' => 'Only pending B2B items can be rejected.'];
        }

        $record->update([
            'status' => 'Rejected',
            'rejected_by_admin_id' => Auth::guard('admin')->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'approved_by_admin_id' => null,
            'approved_at' => null,
        ]);

        return ['ok' => true, 'message' => 'B2B request rejected.'];
    }
}
