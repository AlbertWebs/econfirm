<?php

namespace App\Http\Controllers;

use App\Models\TariffQuery;
use App\Services\TariffCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TariffQueryLogController extends Controller
{
    /**
     * Log each tariffs calculator submission (inputs + computed breakdown when valid).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'integer', 'min:1', 'max:999999999'],
            'rail' => ['required', 'string', 'in:b2c,b2b'],
        ]);

        $ip = $request->ip();
        $ua = Str::limit((string) $request->userAgent(), 65000, '');

        if ($validator->fails()) {
            TariffQuery::query()->create([
                'amount_kes' => is_numeric($request->input('amount')) ? (int) $request->input('amount') : null,
                'rail' => in_array($request->input('rail'), ['b2c', 'b2b'], true) ? (string) $request->input('rail') : null,
                'commission_kes' => null,
                'mpesa_fee_kes' => null,
                'total_kes' => null,
                'error_message' => Str::limit((string) ($validator->errors()->first() ?? 'validation_failed'), 512, ''),
                'ip_address' => $ip,
                'user_agent' => $ua,
            ]);

            throw new ValidationException($validator);
        }

        $data = $validator->validated();
        $principal = (int) $data['amount'];
        $rail = (string) $data['rail'];

        try {
            $out = TariffCalculatorService::compute($principal, $rail);
        } catch (\InvalidArgumentException $e) {
            TariffQuery::query()->create([
                'amount_kes' => $principal,
                'rail' => $rail,
                'commission_kes' => null,
                'mpesa_fee_kes' => null,
                'total_kes' => null,
                'error_message' => Str::limit($e->getMessage(), 512, ''),
                'ip_address' => $ip,
                'user_agent' => $ua,
            ]);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        TariffQuery::query()->create([
            'amount_kes' => $out['principal'],
            'rail' => $out['rail'],
            'commission_kes' => $out['commission'],
            'mpesa_fee_kes' => $out['mpesa_fee'],
            'total_kes' => $out['total'],
            'error_message' => null,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);

        return response()->json(['ok' => true]);
    }
}
