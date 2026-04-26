<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingContact;
use App\Models\MpesaStkPush;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StkContactController extends Controller
{
    public function index(Request $request)
    {
        $this->syncFromStkAttempts();

        $q = trim((string) $request->get('q', ''));
        $query = MarketingContact::query()->orderByDesc('last_stk_attempt_at')->orderByDesc('updated_at');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('phone', 'like', '%'.$q.'%')
                    ->orWhere('name', 'like', '%'.$q.'%');
            });
        }

        $contacts = $query->paginate(50)->withQueryString();
        $totalUnique = MarketingContact::query()->count();

        return view('admin.stk-contacts.index', compact('contacts', 'q', 'totalUnique'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'contacts_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ]);

        $rows = $this->readImportRows($request->file('contacts_file')->getRealPath());
        $imported = 0;
        foreach ($rows as $row) {
            $phone = $this->normalizePhone($row['phone'] ?? null);
            if ($phone === null) {
                continue;
            }

            $name = isset($row['name']) && is_string($row['name']) ? trim($row['name']) : null;
            MarketingContact::query()->updateOrCreate(
                ['phone' => $phone],
                [
                    'name' => $name !== '' ? $name : null,
                    'last_imported_at' => now(),
                    'source' => 'import',
                ]
            );
            $imported++;
        }

        AdminActivityLogger::log('stk_contacts.import', MarketingContact::class, null, ['rows_imported' => $imported]);

        return redirect()
            ->route('admin.stk-contacts.index')
            ->with('status', 'Imported and merged '.$imported.' contact(s).');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->buildRows($request);
        AdminActivityLogger::log('stk_contacts.export_csv', MarketingContact::class, null, ['rows' => count($rows)]);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'phone', 'stk_attempts', 'last_stk_attempt_at']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['name'], $r['phone'], $r['attempts'], $r['last_attempt_at']]);
            }
            fclose($out);
        }, 'stk-contacts-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportVcf(Request $request): StreamedResponse
    {
        $rows = $this->buildRows($request);
        AdminActivityLogger::log('stk_contacts.export_vcf', MarketingContact::class, null, ['rows' => count($rows)]);

        return response()->streamDownload(function () use ($rows) {
            $i = 1;
            foreach ($rows as $r) {
                $name = trim((string) $r['name']) !== '' ? trim((string) $r['name']) : 'STK Lead '.$i;
                echo "BEGIN:VCARD\r\n";
                echo "VERSION:3.0\r\n";
                echo 'FN:'.$name."\r\n";
                echo 'TEL;TYPE=CELL:'.$r['phone']."\r\n";
                echo 'NOTE:STK attempts='.$r['attempts'].'; last_attempt_at='.$r['last_attempt_at']."\r\n";
                echo "END:VCARD\r\n";
                $i++;
            }
        }, 'stk-contacts-'.now()->format('Ymd-His').'.vcf', ['Content-Type' => 'text/vcard; charset=UTF-8']);
    }

    /**
     * @return array<int, array{name: string, phone: string, attempts: int, last_attempt_at: string}>
     */
    protected function buildRows(Request $request): array
    {
        $q = trim((string) $request->get('q', ''));
        $query = MarketingContact::query()->orderByDesc('last_stk_attempt_at')->orderByDesc('updated_at');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('phone', 'like', '%'.$q.'%')
                    ->orWhere('name', 'like', '%'.$q.'%');
            });
        }

        return $query->get()->map(function (MarketingContact $row) {
            return [
                'name' => (string) ($row->name ?? ''),
                'phone' => (string) $row->phone,
                'attempts' => (int) $row->stk_attempts,
                'last_attempt_at' => optional($row->last_stk_attempt_at)->format('Y-m-d H:i:s') ?? '',
            ];
        })->all();
    }

    protected function syncFromStkAttempts(): void
    {
        $rows = MpesaStkPush::query()
            ->selectRaw('phone, COUNT(*) as attempts, MAX(created_at) as last_attempt_at')
            ->whereNotNull('phone')
            ->whereRaw("TRIM(phone) <> ''")
            ->groupBy('phone')
            ->get();

        foreach ($rows as $row) {
            $phone = $this->normalizePhone((string) $row->phone);
            if ($phone === null) {
                continue;
            }

            MarketingContact::query()->updateOrCreate(
                ['phone' => $phone],
                [
                    'stk_attempts' => (int) $row->attempts,
                    'last_stk_attempt_at' => $row->last_attempt_at,
                    'source' => 'stk',
                ]
            );
        }
    }

    /**
     * @return Collection<int, array{phone: mixed, name: mixed}>
     */
    protected function readImportRows(string $realPath): Collection
    {
        $spreadsheet = IOFactory::load($realPath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        if ($data === []) {
            return collect();
        }

        $header = array_shift($data);
        $index = [];
        if (is_array($header)) {
            foreach ($header as $col => $val) {
                $h = strtolower(trim((string) $val));
                if (in_array($h, ['phone', 'phone_number', 'mobile', 'msisdn', 'tel'], true)) {
                    $index['phone'] = $col;
                }
                if (in_array($h, ['name', 'full_name', 'contact_name'], true)) {
                    $index['name'] = $col;
                }
            }
        }

        if (! isset($index['phone'])) {
            $index['phone'] = 'A';
        }

        return collect($data)->map(function ($row) use ($index) {
            return [
                'phone' => is_array($row) ? ($row[$index['phone']] ?? null) : null,
                'name' => isset($index['name']) && is_array($row) ? ($row[$index['name']] ?? null) : null,
            ];
        });
    }

    protected function normalizePhone(mixed $raw): ?string
    {
        $phone = preg_replace('/\s+/', '', (string) $raw);
        if ($phone === '') {
            return null;
        }
        $phone = ltrim($phone, '+');
        if (str_starts_with($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        }
        if (preg_match('/^254\d{9}$/', $phone)) {
            return $phone;
        }

        return null;
    }
}
