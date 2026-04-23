<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with transactions.
     *
     * @return \Illuminate\View\View
     */
    public function viewTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('dashboard.view', compact('transaction'));
    }

    /**
     * Dedicated create-escrow page (avoids the homepage hero tab UX on /dashboard).
     */
    public function createTransaction(): \Illuminate\View\View
    {
        return view('dashboard.create-transaction');
    }

    public function index(): \Illuminate\View\View
    {
        $variants = $this->kenyaPhoneVariantsForUser(Auth::user());
        if ($variants === []) {
            $transactions = Transaction::query()->whereRaw('1=0')->paginate(10);
            $AllCompletedTransactionsCount = 0;
            $AllPendingTransactionsCount = 0;
            $AllPendingTransactionsAmount = 0;
            $recentActivities = collect();
        } else {
            $senderScope = Transaction::query()->whereIn('sender_mobile', $variants);
            $transactions = (clone $senderScope)->orderByDesc('id')->paginate(10);
            $AllCompletedTransactionsCount = (clone $senderScope)->where('status', 'Completed')->count();
            $AllPendingTransactionsCount = (clone $senderScope)->where('status', '!=', 'Completed')->count();
            $AllPendingTransactionsAmount = (float) (clone $senderScope)->where('status', '!=', 'Completed')->sum('transaction_amount');
            $recentActivities = $this->buildRecentActivityFromTransactions(
                Transaction::query()
                    ->where(function ($q) use ($variants) {
                        $q->whereIn('sender_mobile', $variants)
                            ->orWhereIn('receiver_mobile', $variants);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(8)
                    ->get()
            );
        }

        return view('dashboard.index', compact(
            'transactions',
            'AllCompletedTransactionsCount',
            'AllPendingTransactionsCount',
            'AllPendingTransactionsAmount',
            'recentActivities'
        ));
    }

    /**
     * @return list<string>
     */
    protected function kenyaPhoneVariantsForUser(?User $user): array
    {
        if (! $user || $user->phone === null || $user->phone === '') {
            return [];
        }

        $normalized = SmsService::normalizeKenyaTo254((string) $user->phone);
        if (! preg_match('/^254\d{9}$/', $normalized)) {
            return array_values(array_unique(array_filter([trim((string) $user->phone)])));
        }

        return array_values(array_unique([
            $normalized,
            '0' . substr($normalized, 3),
            substr($normalized, 3),
        ]));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Transaction>  $rows
     * @return \Illuminate\Support\Collection<int, array{title: string, at: \Carbon\Carbon|null, label: string, row_class: string, dot_class: string, badge_class: string}>
     */
    protected function buildRecentActivityFromTransactions($rows)
    {
        return $rows->map(function (Transaction $t) {
            $status = (string) ($t->status ?? '');
            $label = 'Updated';
            $rowClass = 'bg-primary bg-opacity-10';
            $dotClass = 'bg-primary';
            $badgeClass = 'bg-primary';

            if (strcasecmp($status, 'Completed') === 0) {
                $label = 'Completed';
                $rowClass = 'bg-success bg-opacity-10';
                $dotClass = 'bg-success';
                $badgeClass = 'bg-success';
            } elseif (strcasecmp($status, 'pending') === 0
                || strcasecmp($status, 'stk_initiated') === 0) {
                $label = 'Pending';
                $rowClass = 'bg-warning bg-opacity-10';
                $dotClass = 'bg-warning';
                $badgeClass = 'bg-warning text-dark';
            } elseif (stripos($status, 'funded') !== false || strcasecmp($status, 'Escrow Funded') === 0) {
                $label = 'Funded';
                $rowClass = 'bg-info bg-opacity-10';
                $dotClass = 'bg-info';
                $badgeClass = 'bg-info text-dark';
            } elseif (strcasecmp($status, 'cancelled') === 0 || strcasecmp($status, 'canceled') === 0) {
                $label = 'Cancelled';
                $rowClass = 'bg-danger bg-opacity-10';
                $dotClass = 'bg-danger';
                $badgeClass = 'bg-danger';
            }

            $tid = (string) ($t->transaction_id ?? $t->id);
            $type = (string) ($t->transaction_type ?? 'escrow');
            $title = "Transaction {$tid} ({$type}) — {$status}";

            return [
                'title' => $title,
                'at' => $t->updated_at ?? $t->created_at,
                'label' => $label,
                'row_class' => $rowClass,
                'dot_class' => $dotClass,
                'badge_class' => $badgeClass,
            ];
        });
    }

    public function approveTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('dashboard.approve', compact('transaction'));
    }
    public function update(Request $request): JsonResponse
    {
        $user = User::query()->findOrFail(Auth::id());

        if ($request->input('update_section') === 'notifications') {
            if (! Schema::hasColumn('users', 'notify_email')) {
                return response()->json([
                    'message' => 'Notification preferences are not available yet. Run database migrations.',
                ], 422);
            }
            $request->validate([
                'notify_email' => 'boolean',
                'notify_sms' => 'boolean',
            ]);
            $user->notify_email = $request->boolean('notify_email');
            $user->notify_sms = $request->boolean('notify_sms');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Your notification preferences have been saved.',
                'user' => $this->userProfilePayload($user->fresh()),
            ]);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'company'  => 'nullable|string|max:255',
            'street'   => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'zip'      => 'nullable|string|max:20',
        ]);

        if (! empty($validated['phone'])) {
            $normalized = SmsService::normalizeKenyaTo254($validated['phone']);
            if (preg_match('/^254\d{9}$/', $normalized)) {
                $validated['phone'] = $normalized;
            }
        } else {
            $validated['phone'] = null;
        }

        $user->update($validated);

        $user = $user->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Your profile has been saved. All changes are stored on your account.',
            'user' => $this->userProfilePayload($user),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function userProfilePayload(User $user): array
    {
        $hasNotify = Schema::hasColumn('users', 'notify_email')
            && Schema::hasColumn('users', 'notify_sms');

        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'street' => $user->street,
            'city' => $user->city,
            'state' => $user->state,
            'zip' => $user->zip,
            'notify_email' => $hasNotify ? (bool) $user->notify_email : true,
            'notify_sms' => $hasNotify ? (bool) $user->notify_sms : false,
        ];
    }

    
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout(); // ✅ Force logout

        return response()->json([
            'message' => 'Password updated successfully. Redirecting to login...',
            'redirect' => route('login') // Include login URL in response
        ]);
    }
    public function editProfile($sender_mobile)
    {
        $transaction = Transaction::where('sender_mobile', $sender_mobile)->firstOrFail();
        // 
        return view('dashboard.edit-profile', compact('transaction','sender_mobile'));

    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'phone'   => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'street'  => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
            'state'   => 'nullable|string|max:255',
            'zip'     => 'nullable|string|max:20',
        ]);

        // Generate a secure random password
        $password = Str::random(10);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'company'  => $request->company,
            'street'   => $request->street,
            'city'     => $request->city,
            'state'    => $request->state,
            'zip'      => $request->zip,
            'password' => Hash::make($password),
        ]);

        // Send login details via SMS
        $smsService = new SmsService();
        $message = "Welcome to eConfirm, {$user->name}. Your login email is {$user->email} and password is: {$password}";
        $smsService->send($user->phone, $message);

        // Log the user in immediately
        Auth::login($user);

        return response()->json([
            'message' => 'Account created and logged in successfully.',
            'user' => $user
        ]);
    }

    public function customLogin(Request $request): JsonResponse
    {
        \Log::info('Login Attempt:', $request->all()); // or use dd($request->all());
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        $validator = Validator::make($credentials, [
            'email'    => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input.'], 422);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login successful.']);
        }

        return response()->json(['error' => 'Invalid credentials.'], 401);
    }

}