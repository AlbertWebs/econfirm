<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\MpesaStkPush;
use App\Models\Otp;
use App\Models\Page;
use App\Models\ScamReport;
use App\Models\ScamReportComment;
use App\Models\ScamReportLike;
use App\Models\SupportHelpItem;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MpesaService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        return $this->homePageView('home', 'front.welcome');
    }

    public function indexV2()
    {
        return $this->homePageView('home-v2', 'front.welcome-v2');
    }

    /**
     * Published CMS page replaces the default Blade homepage for the given slug (e.g. "home" for /, "home-v2" for /v2).
     */
    protected function homePageView(string $slug, string $fallbackView)
    {
        $page = Page::query()->where('slug', $slug)->where('is_published', true)->first();
        if ($page) {
            return view('front.home-cms', compact('page'));
        }

        return view($fallbackView);
    }

    public function portal()
    {
        $phone = $this->resolvePortalPhone();
        $transactions = collect();
        $completedCount = 0;
        $activeCount = 0;
        $totalValue = 0;

        if ($phone) {
            $variants = [$phone, '0'.substr($phone, 3), substr($phone, 3)];
            $transactions = Transaction::query()
                ->whereIn('sender_mobile', $variants)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();

            $completedCount = (int) $transactions->where('status', 'Completed')->count();
            $activeCount = (int) $transactions->whereIn('status', ['pending', 'stk_initiated', 'Escrow Funded'])->count();
            $totalValue = (float) $transactions->sum('transaction_amount');
        }

        return view('process.portal', [
            'portalPhone' => $phone,
            'transactions' => $transactions,
            'completedCount' => $completedCount,
            'activeCount' => $activeCount,
            'totalValue' => $totalValue,
        ]);
    }

    public function portalSendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $normalized = SmsService::normalizeKenyaTo254($validated['phone']);
        if (! preg_match('/^254\d{9}$/', $normalized)) {
            return response()->json([
                'success' => false,
                'message' => 'Enter a valid Kenya number (+254..., 07..., or 01...).',
            ], 422);
        }

        $otp = Otp::createForPhone($normalized, 10);
        $sms = new SmsService;
        $message = "Your eConfirm portal code is: {$otp->otp_code}. Valid for 10 minutes.";
        $result = $sms->send($normalized, $message, 'portal-otp-'.$normalized);

        if (! is_array($result) || ! SmsService::resultIndicatesSuccess($result)) {
            return response()->json([
                'success' => false,
                'message' => 'Could not send OTP right now. Please try again.',
            ], 422);
        }

        session([
            'portal_otp_phone' => $normalized,
            'portal_otp_sent_at' => now()->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your phone.',
        ]);
    }

    public function portalVerifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $phone = session('portal_otp_phone');
        if (! $phone || ! preg_match('/^254\d{9}$/', (string) $phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Request OTP first.',
            ], 422);
        }

        $otpRecord = Otp::verify((string) $phone, (string) $validated['otp']);
        if (! $otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        session([
            'portal_phone' => $phone,
            'portal_phone_verified' => true,
        ]);
        session()->forget(['portal_otp_phone', 'portal_otp_sent_at']);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully.',
        ]);
    }

    /**
     * Single-page feature detail (hash navigation: #secure-transactions, etc.).
     */
    public function features()
    {
        return view('front.features');
    }

    // Legalities
    public function termsAndConditions()
    {
        return $this->pageFromCmsOrFallback('terms-and-conditions', 'front.terms-and-conditions');
    }

    public function privacyPolicy()
    {
        return $this->pageFromCmsOrFallback('privacy-policy', 'front.privacy-policy');
    }

    public function complience()
    {
        return $this->pageFromCmsOrFallback('complience', 'front.complience');
    }

    public function security()
    {
        return $this->pageFromCmsOrFallback('security', 'front.security');
    }

    public function scamWatchTermsOfUse()
    {
        return view('front.scam-watch-terms-of-use');
    }

    public function support()
    {
        return $this->pageFromCmsOrFallback('support', 'front.support');
    }

    public function help()
    {
        return $this->pageFromCmsOrFallback('help', 'front.help');
    }

    /**
     * @return array<string, mixed>
     */
    protected function supportHelpViewData(string $slug): array
    {
        return match ($slug) {
            'support' => [
                'quickHelpItems' => SupportHelpItem::query()->published()->quickHelp()->ordered()->get(),
            ],
            'help' => [
                'faqItems' => SupportHelpItem::query()->published()->helpFaq()->ordered()->get(),
            ],
            default => [],
        };
    }

    /**
     * Render a CMS page when published; otherwise fall back to the legacy Blade view.
     */
    protected function pageFromCmsOrFallback(string $slug, string $fallbackView)
    {
        $page = Page::query()->where('slug', $slug)->where('is_published', true)->first();
        $extra = $this->supportHelpViewData($slug);
        if ($page) {
            return view('front.page', array_merge(['page' => $page], $extra));
        }

        return view($fallbackView, $extra);
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactSubmission::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
        ]);
    }

    public function sitemap()
    {
        $scamWatchUrls = $this->scamWatchSitemapEntries();

        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('scam.watch'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('scam.watch.report'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.85',
            ],
            ...$scamWatchUrls,
            [
                'loc' => route('support'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('help'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('contact'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('terms.conditions'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('privacy.policy'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('security'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('complience'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('api-documentation'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
        ];

        return response()->view('front.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }

    public function scamWatch()
    {
        $reports = ScamReport::withCount('likes')
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categoryCounts = ScamReport::query()
            ->whereIn('status', ['approved', 'pending'])
            ->selectRaw('category, COUNT(*) as cnt')
            ->groupBy('category')
            ->pluck('cnt', 'category');

        return view('front.scam-watch', compact('reports', 'categoryCounts'));
    }

    public function scamWatchReportForm()
    {
        return view('front.report-a-scam');
    }

    public function scamWatchShow(ScamReport $report, ?string $slug = null)
    {
        $expected = $report->seoSlug();
        if ($slug !== $expected) {
            return redirect()->route('scam.watch.show', ['report' => $report, 'slug' => $expected], 301);
        }

        $report->loadCount('likes');
        $report->loadCount([
            'comments as comments_count' => fn ($q) => $q->where('is_hidden', false),
        ]);

        $related = ScamReport::withCount('likes')
            ->visible()
            ->where('category', $report->category)
            ->where('id', '!=', $report->id)
            ->orderByDesc('report_count')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $comments = ScamReportComment::query()
            ->where('scam_report_id', $report->id)
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with([
                'children' => fn ($q) => $q
                    ->where('is_hidden', false)
                    ->orderBy('created_at', 'asc'),
            ])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $canonicalUrl = route('scam.watch.show', ['report' => $report, 'slug' => $expected]);
        $pageTitle = $this->scamReportPageTitle($report);
        $metaDescription = $this->scamReportMetaDescription($report);

        return view('front.scam-watch-report', compact(
            'report',
            'related',
            'comments',
            'canonicalUrl',
            'pageTitle',
            'metaDescription'
        ));
    }

    public function postScamReportComment(Request $request, ScamReport $report)
    {
        $validated = $request->validate([
            'author_name' => 'nullable|string|max:80',
            'author_email' => 'nullable|email|max:255',
            'body' => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|integer|exists:scam_report_comments,id',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId !== null) {
            $parent = ScamReportComment::query()->findOrFail($parentId);
            if ((int) $parent->scam_report_id !== (int) $report->id) {
                return back()
                    ->withErrors(['body' => 'Invalid comment thread selected.'])
                    ->withInput();
            }
        }

        ScamReportComment::query()->create([
            'scam_report_id' => $report->id,
            'parent_id' => $parentId,
            'author_name' => filled($validated['author_name'] ?? null) ? trim((string) $validated['author_name']) : null,
            'author_email' => filled($validated['author_email'] ?? null) ? trim((string) $validated['author_email']) : null,
            'body' => trim((string) $validated['body']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_hidden' => false,
        ]);

        return back()->with('status', 'Comment posted.');
    }

    public function scamWatchCategory(string $category)
    {
        if (! array_key_exists($category, ScamReport::CATEGORY_LABELS)) {
            abort(404);
        }

        $label = ScamReport::CATEGORY_LABELS[$category];

        $reports = ScamReport::withCount('likes')
            ->visible()
            ->where('category', $category)
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $canonicalUrl = route('scam.watch.category', ['category' => $category]);
        $pageTitle = $label.' — reported scams & numbers | eConfirm Scam Watch';
        $metaDescription = 'Browse community-reported '.$label.' on eConfirm: fake websites, phone numbers, and emails users flagged to help others stay safe.';

        return view('front.scam-watch-category', compact(
            'reports',
            'category',
            'label',
            'canonicalUrl',
            'pageTitle',
            'metaDescription'
        ));
    }

    /**
     * @return array<int, array{loc: string, lastmod: string, changefreq: string, priority: string}>
     */
    private function scamWatchSitemapEntries(): array
    {
        $lastmod = now()->format('Y-m-d');
        $entries = [];

        $categories = ScamReport::query()
            ->visible()
            ->select('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $cat) {
            if (! array_key_exists($cat, ScamReport::CATEGORY_LABELS)) {
                continue;
            }
            $entries[] = [
                'loc' => route('scam.watch.category', ['category' => $cat]),
                'lastmod' => $lastmod,
                'changefreq' => 'daily',
                'priority' => '0.85',
            ];
        }

        ScamReport::query()
            ->visible()
            ->select(['id', 'website', 'phone', 'reported_email', 'report_type', 'updated_at'])
            ->orderBy('id')
            ->chunk(500, function ($reports) use (&$entries, $lastmod) {
                foreach ($reports as $r) {
                    $entries[] = [
                        'loc' => route('scam.watch.show', ['report' => $r, 'slug' => $r->seoSlug()]),
                        'lastmod' => $r->updated_at?->format('Y-m-d') ?? $lastmod,
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
            });

        return $entries;
    }

    private function scamReportPageTitle(ScamReport $report): string
    {
        $value = Str::limit((string) $report->reported_value, 55, '…');
        $type = match ($report->report_type) {
            'website' => 'Scam website',
            'phone' => 'Scam phone number',
            default => 'Scam email',
        };

        return $value.' — '.$type.' ('.$report->category_label.') | eConfirm Scam Watch';
    }

    private function scamReportMetaDescription(ScamReport $report): string
    {
        $summary = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($report->description))), 150, '…');
        $value = Str::limit((string) $report->reported_value, 80, '…');

        return 'Reported '.$report->category_label.': '.$value.'. '.$summary;
    }

    public function submitScamReport(Request $request)
    {
        // Validate the request
        // Identifier fields use x-show in the form but stay in the DOM, so stray values
        // (e.g. a long email left in "phone" after switching type) must not be validated.
        $validated = $request->validate([
            'report_type' => 'required|in:website,phone,email',
            'website' => 'exclude_unless:report_type,website|required_if:report_type,website|string|max:255',
            'phone' => 'exclude_unless:report_type,phone|required_if:report_type,phone|string|max:20',
            'reported_email' => 'exclude_unless:report_type,email|required_if:report_type,email|email|max:255',
            'category' => 'required|string|in:ecommerce,services,investment,job,romance,other',
            'category_other' => 'required_if:category,other|nullable|string|max:255',
            'description' => 'required|string|max:5000',
            'email' => 'nullable|email|max:255',
            'reporter_phone' => 'nullable|string|max:40',
            'date_of_incident' => 'nullable|date',
        ]);

        if ($validated['category'] !== 'other') {
            $validated['category_other'] = null;
        }

        // Check if this report already exists
        $existingReport = ScamReport::where('report_type', $validated['report_type'])
            ->where(function ($query) use ($validated) {
                if ($validated['report_type'] === 'website') {
                    $query->where('website', $validated['website']);
                } elseif ($validated['report_type'] === 'phone') {
                    $query->where('phone', $validated['phone']);
                } elseif ($validated['report_type'] === 'email') {
                    $query->where('reported_email', $validated['reported_email']);
                }
            })
            ->first();

        if ($existingReport) {
            // Increment report count
            $existingReport->increment('report_count');
        } else {
            // Await review; public listing still shows pending entries (see ScamReport::scopeVisible).
            $validated['status'] = 'pending';
            ScamReport::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted successfully. It will be reviewed before being published.',
        ]);
    }

    public function likeScamReport($id)
    {
        $report = ScamReport::findOrFail($id);
        $ipAddress = request()->ip();

        // Check if user already liked this report
        $existingLike = ScamReportLike::where('scam_report_id', $id)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingLike) {
            return response()->json([
                'success' => false,
                'message' => 'You have already liked this report.',
                'likes_count' => $report->likes()->count(),
            ]);
        }

        // Create new like
        ScamReportLike::create([
            'scam_report_id' => $id,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'likes_count' => $report->fresh()->likes()->count(),
        ]);
    }

    // generateUniqueTransactionId
    private function generateUniqueTransactionId(): string
    {
        do {
            $transactionId = 'E-'.strtoupper(bin2hex(random_bytes(3))); // Generates a 6-character random string
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    // calculate tranaction fee calculateTransactionFee
    private function calculateTransactionFee(float $amount): float
    {
        // Example fee calculation: 1% of the transaction amount
        $feePercentage = 0.01; // 1%

        return round($amount * $feePercentage, 2);
    }

    public function submitTransaction(Request $request): JsonResponse
    {
        // Create code to generate unique transaction ID and check if it exists, if it exists, generate a new one the format is ESCROW-ENTRY-veryrandom5-digitnumber
        $transactionId = $this->generateUniqueTransactionId();

        // Validate input
        $validated = $request->validate([
            'transaction-type' => 'required|string',
            'transaction-amount' => 'required|numeric|min:1',
            'sender-mobile' => ['required', 'string', 'max:20', 'regex:/^(?:\+?254|0)(?:7|1)\d{8}$/'],
            'receiver-mobile' => ['required', 'string', 'max:20', 'regex:/^(?:\+?254|0)(?:7|1)\d{8}$/'],
            'transaction-details' => 'nullable|string',
            'payment-method' => 'required|string',
        ], [
            'sender-mobile.regex' => 'Enter a valid Kenya number starting with +254 or 07/01.',
            'receiver-mobile.regex' => 'Enter a valid Kenya number starting with +254 or 07/01.',
        ]);

        $validated['sender-mobile'] = SmsService::normalizeKenyaTo254(trim((string) $validated['sender-mobile']));
        $validated['receiver-mobile'] = SmsService::normalizeKenyaTo254(trim((string) $validated['receiver-mobile']));
        session(['portal_phone' => $validated['sender-mobile']]);

        // Save transaction to database add transaction_id to the transaction
        $transaction = Transaction::create([
            'transaction_fee' => $this->calculateTransactionFee($validated['transaction-amount']),
            'transaction_id' => $transactionId,
            'payment_method' => $validated['payment-method'],
            'paybill_till_number' => $request['paybill-till-number'],
            'transaction_type' => $validated['transaction-type'],
            'transaction_amount' => $validated['transaction-amount'],
            'sender_mobile' => $validated['sender-mobile'],
            'receiver_mobile' => $validated['receiver-mobile'],
            'transaction_details' => $validated['transaction-details'] ?? null,
            'status' => 'pending',
        ]);

        // Create user to user table with some of this data

        // Log the transaction creation
        \Log::info('Transaction created', [
            'transaction_id' => $transaction->transaction_id,
            'transaction_fee' => $transaction->transaction_fee,
            'amount' => $transaction->transaction_amount,
            'sender_mobile' => $transaction->sender_mobile,
            'receiver_mobile' => $transaction->receiver_mobile,
            'status' => $transaction->status,
        ]);
        // i want to save the chackout_request_id and merchant_request_id to the transaction from mpesa service
        $transaction->checkout_request_id = null; // Initialize as null
        $transaction->merchant_request_id = null; // Initialize as null
        $transaction->save();

        // Use MpesaService for STK push
        $mpesa = new MpesaService;
        $mpesaResponse = $mpesa->stkPush($transaction);
        // dd($mpesaResponse); // Debugging line, remove in production

        if ($mpesaResponse['success']) {
            $transaction->status = 'stk_initiated';
            $transaction->checkout_request_id = $mpesaResponse['data']['CheckoutRequestID'] ?? null;
            $transaction->merchant_request_id = $mpesaResponse['data']['MerchantRequestID'] ?? null;
            $transaction->save();

            try {
                (new SmsService)->notifyEscrowStkInitiated($transaction->fresh());
            } catch (\Throwable $e) {
                \Log::error('Escrow STK initiation SMS failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction submitted and STK push initiated! Check your phone for pin confirmation.',
                'CheckoutRequestID' => $mpesaResponse['data']['CheckoutRequestID'] ?? null,
            ]);
        } else {
            $transaction->status = 'stk_failed';
            $transaction->save();

            \Log::error('Transaction saved but STK push failed', [
                'transaction_id' => $transaction->transaction_id,
                'mpesa_success' => $mpesaResponse['success'] ?? null,
                'mpesa_message' => $mpesaResponse['message'] ?? null,
                'mpesa_data' => $mpesaResponse['data'] ?? null,
            ]);

            $detail = $mpesaResponse['message'] ?? 'Unknown M-Pesa error';

            return response()->json([
                'success' => false,
                'message' => 'Transaction saved, but STK push failed: '.$detail,
            ]);
        }
    }

    public function transactionStatus($id)
    {
        $stk = MpesaStkPush::where('checkout_request_id', $id)->first();

        if (! $stk) {
            return response()->json([
                'success' => false,
                'status' => 'unknown',
                'message' => 'Payment session not found.',
            ], 404);
        }

        if ($stk->status === 'Failed') {
            return response()->json([
                'success' => false,
                'status' => 'Failed',
                'message' => 'Payment was declined or cancelled.',
            ]);
        }

        $allowFundWithoutCallbackItems = false;

        if ($stk->status !== 'Success') {
            // Querying STK too soon after the push often returns ResultDesc "still under processing"
            // and scares users who have not seen the phone prompt yet. Defer the first real query briefly.
            $minAgeBeforeStkQuerySeconds = 8;
            if ($stk->created_at && abs((int) $stk->created_at->diffInSeconds(now())) < $minAgeBeforeStkQuerySeconds) {
                return response()->json([
                    'success' => true,
                    'status' => 'Pending',
                    'message' => 'M-Pesa prompt sent. Approve the payment on your phone when you get the request, then wait a few seconds for confirmation here.',
                ]);
            }
            // Callback can be delayed/missed; fallback to Daraja STK query.
            $query = (new MpesaService)->stkPushQuery($id);

            if (($query['status'] ?? null) === 'Success') {
                $stk->status = 'Success';
                $stk->result_desc = $query['message'] ?? $stk->result_desc;
                $stk->save();
                $allowFundWithoutCallbackItems = true;
            } elseif (($query['status'] ?? null) === 'Failed') {
                $failMessage = (string) ($query['message'] ?? '');
                if (MpesaService::stkQueryResultDescLooksInProgress($failMessage)) {
                    return response()->json([
                        'success' => true,
                        'status' => 'Pending',
                        'message' => MpesaService::friendlyStkQueryPendingMessage($failMessage),
                    ]);
                }
                $stk->status = 'Failed';
                $stk->result_desc = $failMessage !== '' ? $failMessage : $stk->result_desc;
                $stk->save();

                return response()->json([
                    'success' => false,
                    'status' => 'Failed',
                    'message' => $failMessage !== '' ? $failMessage : 'Payment was declined or cancelled.',
                ]);
            } else {
                $pendingText = $query['message'] ?? 'Awaiting M-Pesa confirmation (after you enter your PIN, this usually takes a few seconds).';

                return response()->json([
                    'success' => true,
                    'status' => 'Pending',
                    'message' => MpesaService::friendlyStkQueryPendingMessage($pendingText),
                ]);
            }
        } elseif (! $this->hasStkCallbackItemMetadata($stk->callback_metadata)) {
            // STK row already Success (e.g. from a prior poll) but no usable metadata yet — confirm with live query.
            $query = (new MpesaService)->stkPushQuery($id);
            if (($query['status'] ?? null) === 'Success') {
                $allowFundWithoutCallbackItems = true;
            } else {
                $pendingText = $query['message'] ?? 'Payment detected. Finalizing on our side…';

                return response()->json([
                    'success' => true,
                    'status' => 'Pending',
                    'message' => MpesaService::friendlyStkQueryPendingMessage($pendingText),
                ]);
            }
        }

        if (! $this->hasStkCallbackItemMetadata($stk->callback_metadata) && ! $allowFundWithoutCallbackItems) {
            return response()->json([
                'success' => true,
                'status' => 'Pending',
                'message' => 'Payment detected. Waiting for M-Pesa callback sync to finalize escrow funding.',
            ]);
        }

        if ($allowFundWithoutCallbackItems) {
            \Log::info('Escrow fund gate: using STK query confirmation (callback items missing or delayed)', [
                'checkout_request_id' => $id,
            ]);
        }

        $transaction = Transaction::where('checkout_request_id', $id)->first();

        if (! $transaction) {
            \Log::error('STK Success but no matching escrow transaction', [
                'checkout_request_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'Success',
                'transaction_id' => null,
                'message' => 'Payment received; transaction record is missing. Contact support with your CheckoutRequestID.',
            ]);
        }

        $alreadyFunded = in_array($transaction->status, ['Escrow Funded', 'Completed'], true);

        if (! $alreadyFunded) {
            $transaction->status = 'Escrow Funded';
            $transaction->save();

            try {
                (new SmsService)->notifyEscrowFunded($transaction->fresh());
            } catch (\Throwable $e) {
                \Log::error('Escrow funded SMS failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction successful.',
            'transaction_id' => $transaction->transaction_id,
            'status' => 'Success',
        ]);
    }

    /**
     * True if M-Pesa CallbackMetadata items look usable (list of Name/Value, or a single item).
     */
    protected function hasStkCallbackItemMetadata(mixed $raw): bool
    {
        if (! is_array($raw) || $raw === []) {
            return false;
        }

        if (isset($raw['Name'])) {
            return true;
        }

        foreach ($raw as $row) {
            if (is_array($row) && isset($row['Name'])) {
                return true;
            }
        }

        return false;
    }

    // createOTP
    public function createOTP(Request $request): JsonResponse
    {
        $transaction = Transaction::where('transaction_id', $request->input('transaction_id'))->first();
        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        $otp = random_int(100000, 999999);
        $message = "Your eConfirm OTP is: {$otp}. Do not share it. Valid for 3 minutes.";

        $destination = $request->filled('phone')
            ? trim((string) $request->input('phone'))
            : (string) $transaction->sender_mobile;

        if ($destination === '') {
            return response()->json([
                'success' => false,
                'message' => 'No phone number available for this transaction.',
            ], 422);
        }

        try {
            $smsService = new SmsService;
            $smsResult = $smsService->send(
                $destination,
                $message,
                $transaction->transaction_id.'-otp'
            );
        } catch (\Throwable $e) {
            \Log::error('createOTP SMS exception', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not send OTP. Please try again.',
            ], 500);
        }

        if (! SmsService::resultIndicatesSuccess($smsResult)) {
            $detail = is_array($smsResult)
                ? (string) ($smsResult['message'] ?? 'SMS gateway error')
                : 'SMS gateway error';

            \Log::warning('createOTP SMS not accepted', [
                'transaction_id' => $transaction->transaction_id,
                'destination' => $destination,
                'sms_result' => $smsResult,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: '.$detail,
            ], 422);
        }

        $transaction->otp = $otp;
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'OTP sent via SMS.',
        ]);
    }

    // approveTransaction(id)
    public function approveTransaction(Request $request, $id)
    {
        // Check if it maches with OTP
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }
        $stkPush = MpesaStkPush::where('checkout_request_id', $transaction->checkout_request_id)->first();

        // Return a view to approve the transaction
        return view('process.approve-transaction', compact('transaction', 'stkPush'));
    }

    public function approveTransactionPost(Request $request, $id)
    {
        // Validate OTP
        $ValidateOTP = Transaction::where('id', $id)->where('otp', $request->otp)->first();
        if (! $ValidateOTP) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ]);
        } else {
            // Check if OTP is expired
            $otpExpiryTime = 3; // in minutes
            $otpCreatedAt = Carbon::parse($ValidateOTP->updated_at);
            $otpExpiryAt = $otpCreatedAt->addMinutes($otpExpiryTime);

            if (now()->greaterThan($otpExpiryAt)) {
                \Log::info('OTP expired', [
                    'transaction_id' => $ValidateOTP->transaction_id,
                    'otp_created_at' => $otpCreatedAt,
                    'current_time' => now(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.',
                ]);
            }

        }
        // Paybill path → B2B to Paybill/Till; M-Pesa path → B2C to recipient phone. SMS only after Daraja accepts the payout request.
        if ($ValidateOTP->payment_method === 'paybill') {
            $mpesa = new MpesaService;
            $b2bResponse = $mpesa->b2b($ValidateOTP);
            if (! $b2bResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $b2bResponse['message'] ?? 'Payout to Paybill/Till could not be started. Try again or contact support.',
                ]);
            }

            $ValidateOTP->status = 'Completed';
            $ValidateOTP->save();

            try {
                (new SmsService)->notifyPartiesAfterApprovedPayout($ValidateOTP->fresh(), true);
            } catch (\Throwable $e) {
                \Log::error('Post-approval SMS failed (B2B)', [
                    'transaction_id' => $ValidateOTP->transaction_id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction approved. Payout to Paybill/Till has been initiated.',
            ]);
        }

        $mpesa = new MpesaService;
        $b2cResponse = $mpesa->b2c($ValidateOTP);
        if (! $b2cResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $b2cResponse['message'] ?? 'Payout to M-Pesa could not be started. Try again or contact support.',
            ]);
        }

        $ValidateOTP->status = 'Completed';
        $ValidateOTP->save();

        try {
            (new SmsService)->notifyPartiesAfterApprovedPayout($ValidateOTP->fresh(), false);
        } catch (\Throwable $e) {
            \Log::error('Post-approval SMS failed (B2C)', [
                'transaction_id' => $ValidateOTP->transaction_id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction approved. Transfer to recipient M-Pesa has been initiated.',
        ]);
    }

    // approveTransaction
    public function rejectTransaction($id)
    {
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }

        // Return a view to reject the transaction
        return view('process.reject-transaction', compact('transaction'));
    }

    // transaction
    public function transaction($id)
    {
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }
        // Get stk where checkout_request_id is the same as the transaction checkout_request_id
        $stkPush = MpesaStkPush::where('checkout_request_id', $transaction->checkout_request_id)->first();

        $transactionSenderRegistered = false;
        if (Schema::hasColumn('users', 'phone')) {
            $transactionSenderRegistered = User::query()
                ->where('phone', $transaction->sender_mobile)
                ->exists();
        }

        return view('process.transaction', compact('transaction', 'stkPush', 'transactionSenderRegistered'));

    }

    // searchTransactions
    public function searchTransactions(Request $request): JsonResponse
    {
        $query = $request->input('id');
        $transactions = Transaction::where('transaction_id', $query)->get();
        // check if transactions exist
        if ($transactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No transactions found for the given Transaction ID.',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
        }
    }

    public function getAPIDocumentation()
    {
        return view('front.api-documentation');
    }

    public function getEContract()
    {
        return view('front.contracts.escrow-agreement');
    }

    /**
     * Test SMS sending to a specific phone number
     *
     * @param  string|null  $phone  Phone number (optional, defaults to +254723014032)
     * @return JsonResponse
     */
    public function testSms(Request $request, $phone = null)
    {
        $phoneNumber = $phone ?? $request->input('phone', '+254723014032');
        $smsMessage = $request->input('message', 'Test SMS from eConfirm. This is a test message to verify SMS service integration.');

        try {
            $smsService = new SmsService;
            $result = $smsService->send($phoneNumber, $smsMessage, 'test-'.time());

            if (isset($result['status']) && $result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => [
                        'phone' => $phoneNumber,
                        'message' => $smsMessage,
                        'response' => $result,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send SMS',
                    'data' => $result,
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('SMS Test Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: '.$e->getMessage(),
            ], 500);
        }
    }

    protected function resolvePortalPhone(): ?string
    {
        if (Auth::check() && ! empty(Auth::user()->phone)) {
            $normalized = SmsService::normalizeKenyaTo254((string) Auth::user()->phone);
            if (preg_match('/^254\d{9}$/', $normalized)) {
                return $normalized;
            }
        }

        $portalPhone = session('portal_phone');
        if (is_string($portalPhone)) {
            $normalized = SmsService::normalizeKenyaTo254($portalPhone);
            if (preg_match('/^254\d{9}$/', $normalized)) {
                return $normalized;
            }
        }

        return null;
    }
}
