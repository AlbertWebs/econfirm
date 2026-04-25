<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VelipayPayment;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StkContactController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = VelipayPayment::query()
            ->selectRaw('phone, COUNT(*) as attempts, MAX(created_at) as last_attempt_at')
            ->whereNotNull('phone')
            ->whereRaw("TRIM(phone) <> ''")
            ->groupBy('phone')
            ->orderByDesc('last_attempt_at');

        if ($q !== '') {
            $query->having('phone', 'like', '%'.$q.'%');
        }

        $contacts = $query->paginate(50)->withQueryString();
        $totalUnique = VelipayPayment::query()
            ->whereNotNull('phone')
            ->whereRaw("TRIM(phone) <> ''")
            ->distinct('phone')
            ->count('phone');

        return view('admin.stk-contacts.index', compact('contacts', 'q', 'totalUnique'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->buildRows($request);
        AdminActivityLogger::log('stk_contacts.export_csv', VelipayPayment::class, null, ['rows' => count($rows)]);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['phone', 'attempts', 'last_attempt_at']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['phone'], $r['attempts'], $r['last_attempt_at']]);
            }
            fclose($out);
        }, 'stk-contacts-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportVcf(Request $request): StreamedResponse
    {
        $rows = $this->buildRows($request);
        AdminActivityLogger::log('stk_contacts.export_vcf', VelipayPayment::class, null, ['rows' => count($rows)]);

        return response()->streamDownload(function () use ($rows) {
            $i = 1;
            foreach ($rows as $r) {
                $phone = preg_replace('/\s+/', '', (string) $r['phone']);
                echo "BEGIN:VCARD\r\n";
                echo "VERSION:3.0\r\n";
                echo 'FN:STK Lead '.$i."\r\n";
                echo 'TEL;TYPE=CELL:'.$phone."\r\n";
                echo 'NOTE:STK attempts='.$r['attempts'].'; last_attempt_at='.$r['last_attempt_at']."\r\n";
                echo "END:VCARD\r\n";
                $i++;
            }
        }, 'stk-contacts-'.now()->format('Ymd-His').'.vcf', ['Content-Type' => 'text/vcard; charset=UTF-8']);
    }

    /**
     * @return array<int, array{phone: string, attempts: int, last_attempt_at: string}>
     */
    protected function buildRows(Request $request): array
    {
        $q = trim((string) $request->get('q', ''));

        $query = VelipayPayment::query()
            ->selectRaw('phone, COUNT(*) as attempts, MAX(created_at) as last_attempt_at')
            ->whereNotNull('phone')
            ->whereRaw("TRIM(phone) <> ''")
            ->groupBy('phone')
            ->orderByDesc('last_attempt_at');

        if ($q !== '') {
            $query->having('phone', 'like', '%'.$q.'%');
        }

        return $query->get()->map(function ($row) {
            return [
                'phone' => (string) $row->phone,
                'attempts' => (int) $row->attempts,
                'last_attempt_at' => optional($row->last_attempt_at)->format('Y-m-d H:i:s') ?? (string) $row->last_attempt_at,
            ];
        })->all();
    }
}
