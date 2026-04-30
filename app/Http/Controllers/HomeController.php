<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\ContactSubmission;
use App\Models\Otp;
use App\Models\Page;
use App\Models\ScamReport;
use App\Models\ScamCommunity;
use App\Models\ScamCommunityAdmin;
use App\Models\ScamReportComment;
use App\Models\ScamReportLike;
use App\Models\SupportHelpItem;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VelipayPayment;
use App\Services\EscrowVelipayFundingService;
use App\Services\SmsService;
use App\Services\StkRequestIpLimiter;
use App\Services\VelipayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $latestBlogs = Blog::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $page = Page::query()->where('slug', $slug)->where('is_published', true)->first();
        if ($page) {
            return view('front.home-cms', compact('page', 'latestBlogs'));
        }

        return view($fallbackView, compact('latestBlogs'));
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

    public function productEscrow(string $product)
    {
        $products = $this->productLandingPages();
        if (! array_key_exists($product, $products)) {
            abort(404);
        }

        $page = $products[$product];
        $cmsPage = Page::query()
            ->where('slug', 'escrow-'.$product)
            ->where('is_published', true)
            ->first();

        if ($cmsPage) {
            return view('front.product-escrow-cms', [
                'page' => $cmsPage,
                'productKey' => $product,
                'productName' => $page['name'],
                'canonicalUrl' => route('escrow.product', ['product' => $product]),
                'seoTitle' => $cmsPage->title ?: $page['seo_title'],
                'seoDescription' => $cmsPage->meta_description ?: $page['seo_description'],
            ]);
        }

        return view('front.product-escrow', [
            'productKey' => $product,
            'productName' => $page['name'],
            'industryKeyword' => $page['industry_keyword'],
            'seoTitle' => $page['seo_title'],
            'seoDescription' => $page['seo_description'],
            'benefits' => $page['benefits'],
            'faqs' => $page['faqs'],
            'canonicalUrl' => route('escrow.product', ['product' => $product]),
        ]);
    }

    /**
     * @return array<string, array{name:string,industry_keyword:string,seo_title:string,seo_description:string,benefits:list<string>,faqs:list<array{q:string,a:string}>}>
     */
    protected function productLandingPages(): array
    {
        $commonBenefits = [
            'Secure M-Pesa escrow flow where funds move only after agreed milestones.',
            'Transaction timeline and evidence trail to reduce fraud disputes.',
            'Dedicated support for sender and receiver when verification is needed.',
        ];
        $commonFaqs = [
            [
                'q' => 'How does eConfirm M-Pesa escrow work?',
                'a' => 'Buyer/sender funds the escrow, both parties track status, and release happens only when delivery terms are met.',
            ],
            [
                'q' => 'Can I use this for marketplace deals like Jiji escrow style transactions?',
                'a' => 'Yes. eConfirm is designed for person-to-person and marketplace transactions where trust and payment protection are needed.',
            ],
        ];

        return [
            'real-estate' => [
                'name' => 'Real Estate Escrow',
                'industry_keyword' => 'property transactions',
                'seo_title' => 'Real Estate Escrow Kenya | eConfirm M-Pesa Escrow for Property Deals',
                'seo_description' => 'Use eConfirm real estate escrow to protect buyers, sellers, and agents in property transactions with secure M-Pesa escrow release conditions.',
                'benefits' => [...$commonBenefits, 'Protect deposit and final payment steps for land, rentals, and home purchases.'],
                'faqs' => $commonFaqs,
            ],
            'vehicle' => [
                'name' => 'Vehicle Escrow',
                'industry_keyword' => 'vehicle sales',
                'seo_title' => 'Vehicle Escrow Kenya | Safe Car Sale Payments with eConfirm',
                'seo_description' => 'Use eConfirm vehicle escrow for safer car, bike, and fleet transfers with secure M-Pesa escrow and fraud protection checks.',
                'benefits' => [...$commonBenefits, 'Ideal for logbook handover workflows in private and dealer vehicle sales.'],
                'faqs' => $commonFaqs,
            ],
            'business' => [
                'name' => 'Business Escrow',
                'industry_keyword' => 'business payments',
                'seo_title' => 'Business Escrow Services Kenya | eConfirm B2B M-Pesa Escrow',
                'seo_description' => 'Reduce business payment risk using eConfirm escrow for supplier invoices, stock delivery, and service milestones.',
                'benefits' => [...$commonBenefits, 'Works for SME supplier agreements and staged B2B deliveries.'],
                'faqs' => $commonFaqs,
            ],
            'ecommerce' => [
                'name' => 'E-commerce Escrow',
                'industry_keyword' => 'online shopping',
                'seo_title' => 'E-commerce Escrow Kenya | Buyer Protection with eConfirm',
                'seo_description' => 'eConfirm e-commerce escrow secures online store purchases and social commerce orders using M-Pesa escrow protection.',
                'benefits' => [...$commonBenefits, 'Useful for Instagram, Facebook, and marketplace-style order protection.'],
                'faqs' => $commonFaqs,
            ],
            'services' => [
                'name' => 'Services Escrow',
                'industry_keyword' => 'service contracts',
                'seo_title' => 'Services Escrow | Secure Service Payments via eConfirm',
                'seo_description' => 'Pay safely for professional services with eConfirm escrow. Release funds after completion and verification.',
                'benefits' => [...$commonBenefits, 'Supports milestone-based service delivery and approval.'],
                'faqs' => $commonFaqs,
            ],
            'freelancer' => [
                'name' => 'Freelancer Escrow',
                'industry_keyword' => 'freelance projects',
                'seo_title' => 'Freelancer Escrow Kenya | Secure Gig Payments with eConfirm',
                'seo_description' => 'Use eConfirm freelancer escrow to protect clients and freelancers on design, development, and remote projects.',
                'benefits' => [...$commonBenefits, 'Prevents non-payment and unfinished delivery risks in freelance jobs.'],
                'faqs' => $commonFaqs,
            ],
            'rental' => [
                'name' => 'Rental Escrow',
                'industry_keyword' => 'rental agreements',
                'seo_title' => 'Rental Escrow Kenya | Safer Deposit and Rent Transactions',
                'seo_description' => 'eConfirm rental escrow protects landlords and tenants for deposits and staged rent-related agreements.',
                'benefits' => [...$commonBenefits, 'Clear terms for deposit release after property or asset handover.'],
                'faqs' => $commonFaqs,
            ],
            'import-export' => [
                'name' => 'Import & Export Escrow',
                'industry_keyword' => 'trade shipments',
                'seo_title' => 'Import Export Escrow | Trade Payment Protection with eConfirm',
                'seo_description' => 'Use eConfirm escrow to reduce risk in import/export deals and shipment milestone payments.',
                'benefits' => [...$commonBenefits, 'Protects buyers and sellers across shipment and documentation milestones.'],
                'faqs' => $commonFaqs,
            ],
            'digital-asset' => [
                'name' => 'Digital Asset Escrow',
                'industry_keyword' => 'digital asset deals',
                'seo_title' => 'Digital Asset Escrow | Domains, Accounts and Digital Goods',
                'seo_description' => 'Secure digital asset transfers with eConfirm escrow for domains, accounts, software licenses, and other digital goods.',
                'benefits' => [...$commonBenefits, 'Release funds only after access credentials and ownership transfer are confirmed.'],
                'faqs' => $commonFaqs,
            ],
            'construction' => [
                'name' => 'Construction Escrow',
                'industry_keyword' => 'construction projects',
                'seo_title' => 'Construction Escrow Kenya | Milestone Payment Security',
                'seo_description' => 'Manage contractor and supplier risks using eConfirm construction escrow for milestone-based payment release.',
                'benefits' => [...$commonBenefits, 'Great for staged project payments tied to inspection milestones.'],
                'faqs' => $commonFaqs,
            ],
            'equipment-machinery' => [
                'name' => 'Equipment / Machinery Escrow',
                'industry_keyword' => 'equipment purchases',
                'seo_title' => 'Equipment Escrow Kenya | Machinery Payment Protection',
                'seo_description' => 'Use eConfirm escrow for equipment and machinery transactions with safer payment release after verification.',
                'benefits' => [...$commonBenefits, 'Reduces risk in high-value machinery and industrial equipment purchases.'],
                'faqs' => $commonFaqs,
            ],
            'tender-contract' => [
                'name' => 'Tender / Contract Escrow',
                'industry_keyword' => 'tender contracts',
                'seo_title' => 'Tender Contract Escrow | Contract Payment Assurance with eConfirm',
                'seo_description' => 'Secure tender and contract-based payments with eConfirm escrow and clear release criteria.',
                'benefits' => [...$commonBenefits, 'Provides neutral payment control for contract milestone commitments.'],
                'faqs' => $commonFaqs,
            ],
            'education-school-fees' => [
                'name' => 'Education / School Fees Escrow',
                'industry_keyword' => 'education payments',
                'seo_title' => 'School Fees Escrow Kenya | Protected Education Payments',
                'seo_description' => 'Use eConfirm escrow for education-related fees and institutional payments requiring trusted release controls.',
                'benefits' => [...$commonBenefits, 'Improves transparency in managed education fee disbursements.'],
                'faqs' => $commonFaqs,
            ],
            'marketplace' => [
                'name' => 'Marketplace Escrow',
                'industry_keyword' => 'marketplace deals',
                'seo_title' => 'Marketplace Escrow Kenya | Jiji Escrow Style Protection by eConfirm',
                'seo_description' => 'Use eConfirm marketplace escrow for safer online buyer-seller transactions, including Jiji-style deals with M-Pesa escrow.',
                'benefits' => [...$commonBenefits, 'Built for peer-to-peer marketplace transactions where trust is limited.'],
                'faqs' => $commonFaqs,
            ],
        ];
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

    /**
     * Parent technology partner (Velinex Labs) — public profile and backlinks.
     */
    public function velinexLabs()
    {
        return view('front.velinex-labs');
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
        $a = random_int(1, 12);
        $b = random_int(1, 12);
        session([
            'contact_math_sum' => $a + $b,
        ]);

        return view('front.contact', [
            'contactMathA' => $a,
            'contactMathB' => $b,
        ]);
    }

    public function submitContact(Request $request)
    {
        $expected = session('contact_math_sum');
        if ($expected === null) {
            return response()->json([
                'success' => false,
                'message' => 'Your form session expired. Please refresh the page and try again.',
            ], 422);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:40',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'math_answer' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        if ((int) $validated['math_answer'] !== (int) $expected) {
            return response()->json([
                'success' => false,
                'message' => 'The security check answer is incorrect. Please try again.',
                'errors' => ['math_answer' => ['The sum does not match.']],
            ], 422);
        }

        session()->forget('contact_math_sum');

        unset($validated['math_answer']);

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
        $reports = ScamReport::with('community')->withCount('likes')
            ->publicListed()
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categoryCounts = ScamReport::query()
            ->publicListed()
            ->selectRaw('category, COUNT(*) as cnt')
            ->groupBy('category')
            ->pluck('cnt', 'category');

        $communities = ScamCommunity::query()
            ->where('is_active', true)
            ->withCount(['reports' => fn ($q) => $q->publicListed()])
            ->orderByDesc('reports_count')
            ->orderBy('name')
            ->limit(12)
            ->get();

        return view('front.scam-watch', compact('reports', 'categoryCounts', 'communities'));
    }

    public function scamWatchReportForm()
    {
        $communities = ScamCommunity::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('front.report-a-scam', compact('communities'));
    }

    /**
     * Download a submitted evidence file (verified reports only; same storage as admin).
     */
    public function scamWatchEvidence(ScamReport $report, int $index): StreamedResponse
    {
        if (! $report->is_verified) {
            abort(404);
        }

        $paths = $report->evidence;
        if (! is_array($paths) || ! isset($paths[$index])) {
            abort(404);
        }
        $path = $paths[$index];
        if (! is_string($path) || $path === '' || str_contains($path, '..')) {
            abort(404);
        }
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path, basename($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function scamWatchShow(ScamReport $report, ?string $slug = null)
    {
        $expected = $report->seoSlug();
        if ($slug !== $expected) {
            return redirect()->route('scam.watch.show', ['report' => $report, 'slug' => $expected], 301);
        }

        $report->load('community');
        $report->loadCount('likes');
        $report->loadCount([
            'comments as comments_count' => fn ($q) => $q->where('is_hidden', false),
        ]);

        $related = ScamReport::with('community')->withCount('likes')
            ->publicListed()
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

        $reports = ScamReport::with('community')->withCount('likes')
            ->publicListed()
            ->where('category', $category)
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $canonicalUrl = route('scam.watch.category', ['category' => $category]);
        $pageTitle = $label.' — reported scams & numbers | eConfirm Scam Alert';
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

    public function scamWatchCommunity(ScamCommunity $community)
    {
        abort_unless($community->is_active, 404);

        $adminRole = auth()->check()
            ? ScamCommunityAdmin::query()
                ->where('scam_community_id', $community->id)
                ->where('user_id', (int) auth()->id())
                ->first()
            : null;
        $isApprovedCommunityAdmin = ($adminRole?->status ?? null) === 'approved';

        $reportsQuery = ScamReport::with('community')->withCount('likes')
            ->where('community_id', $community->id);

        if ($isApprovedCommunityAdmin) {
            // Approved community admins can review pending/rejected community moderation items.
            $reportsQuery->where('status', 'approved');
        } else {
            $reportsQuery->publicListed();
        }

        $reports = $reportsQuery
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $canonicalUrl = route('scam.watch.community', ['community' => $community]);
        $pageTitle = $community->name.' — scam reports | eConfirm Scam Alert';
        $metaDescription = 'Scam reports shared by '.$community->name.'. Browse warnings, discuss in comments, and share alerts with your network.';

        return view('front.scam-watch-community', compact(
            'community',
            'reports',
            'canonicalUrl',
            'pageTitle',
            'metaDescription',
            'adminRole'
        ));
    }

    public function requestScamCommunityAdmin(Request $request, ScamCommunity $community)
    {
        $user = $request->user();
        abort_unless($user, 403);
        abort_unless($community->is_active, 404);

        ScamCommunityAdmin::query()->firstOrCreate([
            'scam_community_id' => $community->id,
            'user_id' => $user->id,
        ], [
            'status' => 'pending',
        ]);

        return back()->with('status', 'Community admin request submitted. A platform admin must approve you before you can moderate posts.');
    }

    public function moderateScamCommunityReport(Request $request, ScamCommunity $community, ScamReport $report)
    {
        $user = $request->user();
        abort_unless($user, 403);
        abort_unless((int) $report->community_id === (int) $community->id, 404);

        $role = ScamCommunityAdmin::query()
            ->where('scam_community_id', $community->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();
        abort_unless($role, 403);

        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
        ]);

        $report->update([
            'community_moderation_status' => $validated['decision'],
            'community_moderated_by_user_id' => $user->id,
            'community_moderated_at' => now(),
        ]);

        return back()->with('status', $validated['decision'] === 'approved'
            ? 'Report approved by community admin and is now eligible for public display.'
            : 'Report rejected by community admin.');
    }

    /**
     * @return array<int, array{loc: string, lastmod: string, changefreq: string, priority: string}>
     */
    private function scamWatchSitemapEntries(): array
    {
        $lastmod = now()->format('Y-m-d');
        $entries = [];

        $categories = ScamReport::query()
            ->publicListed()
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

        ScamCommunity::query()
            ->where('is_active', true)
            ->whereHas('reports', fn ($q) => $q->publicListed())
            ->orderBy('name')
            ->get()
            ->each(function (ScamCommunity $community) use (&$entries, $lastmod): void {
                $entries[] = [
                    'loc' => route('scam.watch.community', ['community' => $community]),
                    'lastmod' => $lastmod,
                    'changefreq' => 'daily',
                    'priority' => '0.75',
                ];
            });

        ScamReport::query()
            ->publicListed()
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

        return $value.' — '.$type.' ('.$report->category_label.') | eConfirm Scam Alert';
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
            'community_id' => 'nullable|integer|exists:scam_communities,id',
            'community_name' => 'nullable|string|max:120',
            'description' => 'required|string|max:5000',
            'email' => 'nullable|email|max:255',
            'reporter_phone' => 'nullable|string|max:40',
            'date_of_incident' => 'nullable|date',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        $evidenceUploads = (array) ($validated['evidence_files'] ?? []);
        unset($validated['evidence_files']);

        if ($validated['category'] !== 'other') {
            $validated['category_other'] = null;
        }

        $communityId = isset($validated['community_id']) ? (int) $validated['community_id'] : null;
        $communityName = trim((string) ($validated['community_name'] ?? ''));
        unset($validated['community_name']);
        if ($communityName !== '' && ! $communityId) {
            $baseSlug = Str::slug($communityName);
            $slug = $baseSlug !== '' ? $baseSlug : 'community';
            $originalSlug = $slug;
            $counter = 2;
            while (ScamCommunity::query()->where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }

            $community = ScamCommunity::query()->create([
                'name' => $communityName,
                'slug' => $slug,
                'is_active' => true,
            ]);
            $communityId = (int) $community->id;
        }
        $validated['community_id'] = $communityId ?: null;

        // Check if this report already exists
        $existingReport = ScamReport::where('report_type', $validated['report_type'])
            ->where('community_id', $validated['community_id'])
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
            if (! empty($validated['community_id'])) {
                // Community flow: requires approved community admin decision before public listing.
                $validated['status'] = 'approved';
                $validated['community_moderation_status'] = 'pending';
            } else {
                // General flow: platform admin moderation.
                $validated['status'] = 'pending';
                $validated['community_moderation_status'] = null;
            }
            $report = ScamReport::create($validated);

            $evidencePaths = [];
            foreach ($evidenceUploads as $file) {
                if ($file && $file->isValid()) {
                    $evidencePaths[] = $file->store("scam-reports/{$report->id}", 'local');
                }
            }
            if ($evidencePaths !== []) {
                $report->update(['evidence' => $evidencePaths]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted successfully. It will be reviewed before being published.',
        ]);
    }

    public function likeScamReport($id)
    {
        $report = ScamReport::publicListed()->where('id', $id)->firstOrFail();
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
        // Keep IDs nullable before payment initiation.
        $transaction->checkout_request_id = null; // Initialize as null
        $transaction->merchant_request_id = null; // Initialize as null
        $transaction->save();

        $clientIp = $request->ip();
        if (StkRequestIpLimiter::isBlocked($clientIp)) {
            return response()->json([
                'success' => false,
                'message' => StkRequestIpLimiter::MESSAGE,
            ], 429);
        }

        // Use VeliPay for STK push
        $velipay = new VelipayService;
        $velipayResponse = $velipay->stkPush($transaction, $clientIp);

        if ($velipayResponse['success']) {
            $paymentId = (string) (($velipayResponse['data']['paymentId'] ?? '') ?: '');
            $transaction->status = 'stk_initiated';
            $transaction->checkout_request_id = $paymentId !== '' ? $paymentId : null;
            $transaction->merchant_request_id = null;
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
                'message' => 'Payment prompt sent. Check your phone and authorize the request.',
                'CheckoutRequestID' => $paymentId !== '' ? $paymentId : null,
            ]);
        } else {
            $transaction->status = 'stk_failed';
            $transaction->save();

            \Log::error('Transaction saved but VeliPay STK push failed', [
                'transaction_id' => $transaction->transaction_id,
                'velipay_success' => $velipayResponse['success'] ?? null,
                'velipay_message' => $velipayResponse['message'] ?? null,
                'velipay_data' => $velipayResponse['data'] ?? null,
            ]);

            $detail = $velipayResponse['message'] ?? 'Unknown payment error';

            return response()->json([
                'success' => false,
                'message' => 'Transaction saved, but payment initiation failed: '.$detail,
            ]);
        }
    }

    public function transactionStatus($id)
    {
        $payment = VelipayPayment::where('velipay_payment_id', $id)->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'status' => 'unknown',
                'message' => 'Payment session not found.',
            ], 404);
        }

        if (in_array(strtolower((string) $payment->status), ['failed', 'cancelled'], true)) {
            return response()->json([
                'success' => false,
                'status' => 'Failed',
                'message' => 'Payment was declined or cancelled.',
            ]);
        }

        if (! in_array(strtolower((string) $payment->status), ['paid', 'settled', 'success'], true)) {
            return response()->json([
                'success' => true,
                'status' => 'Pending',
                'message' => 'Awaiting payment confirmation. If you already approved on phone, give it a moment and retry.',
            ]);
        }

        $transaction = EscrowVelipayFundingService::markFundedByPayment($payment);

        if (! $transaction) {
            return response()->json([
                'success' => true,
                'status' => 'Success',
                'transaction_id' => null,
                'message' => 'Payment received; transaction record is missing. Contact support with your payment ID.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your escrow has been funded. Redirecting…',
            'transaction_id' => $transaction->transaction_id,
            'status' => 'Success',
        ]);
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
        $stkPush = VelipayPayment::where('velipay_payment_id', $transaction->checkout_request_id)->first();

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
            $otpExpiryAt = $otpCreatedAt->copy()->addMinutes($otpExpiryTime);

            if (now()->greaterThan($otpExpiryAt)) {
                \Log::info('OTP expired', [
                    'transaction_id' => $ValidateOTP->transaction_id,
                    'otp_created_at' => $otpCreatedAt->toIso8601String(),
                    'otp_expires_at' => $otpExpiryAt->toIso8601String(),
                    'current_time' => now()->toIso8601String(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.',
                ]);
            }

        }

        if (strcasecmp((string) $ValidateOTP->status, 'Escrow Funded') !== 0) {
            $st = (string) $ValidateOTP->status;

            return response()->json([
                'success' => false,
                'message' => strcasecmp($st, 'Completed') === 0
                    ? 'This transaction is already complete. If the recipient did not receive funds, contact support with your transaction ID.'
                    : 'Escrow must be funded before you can release funds to the recipient. Current status: '.$st,
            ], 422);
        }

        $velipay = new VelipayService;
        $releaseResponse = $velipay->withdrawToPhone($ValidateOTP);
        if (! $releaseResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $releaseResponse['message'] ?? 'Payout could not be started. Try again or contact support.',
            ]);
        }

        $ValidateOTP->status = 'payout_initiated';
        $ValidateOTP->save();

        try {
            (new SmsService)->notifyPartiesAfterApprovedPayout($ValidateOTP->fresh(), false);
        } catch (\Throwable $e) {
            \Log::error('Post-approval SMS failed', [
                'transaction_id' => $ValidateOTP->transaction_id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction approved. Release to recipient has been initiated.',
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
        $stkPush = VelipayPayment::where('velipay_payment_id', $transaction->checkout_request_id)->first();

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
        $apiV1Url = econfirm_api_v1_url();
        $apiRootUrl = econfirm_api_root_url();
        $docsPageUrl = url('/api/documentation');
        $contactPhoneDisplay = (string) site_setting('contact_phone_display', '0748 349995');
        $contactPhoneE164 = (string) site_setting('contact_phone_e164', '+254748349995');
        $contactPhoneHref = 'tel:'.preg_replace('/[^\d+]/', '', $contactPhoneE164);

        return view('front.api-documentation', compact(
            'apiV1Url',
            'apiRootUrl',
            'docsPageUrl',
            'contactPhoneDisplay',
            'contactPhoneHref',
        ));
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
