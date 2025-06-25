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

    public function index(): \Illuminate\View\View
    {
        $transactions = Transaction::where('sender_mobile', Auth::user()->phone)->paginate(10);
        $AllCompletedTransactionsCount = Transaction::where('sender_mobile', Auth::user()->phone)->where('status', '=', 'Completed')->count();
        $AllPendingTransactionsCount = Transaction::where('sender_mobile', Auth::user()->phone)->where('status', '!=', 'Completed')->count();
        //Sum of transaction_amount for all pending transactions
        $AllPendingTransactionsAmount = Transaction::where('sender_mobile', Auth::user()->phone)->where('status', '!=', 'Completed')->sum('transaction_amount');
        return view('dashboard.index', compact('transactions', 'AllCompletedTransactionsCount', 'AllPendingTransactionsCount', 'AllPendingTransactionsAmount'));
    }

    public function approveTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('dashboard.approve', compact('transaction'));
    }
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone'    => 'nullable|string|max:20',
            'company'  => 'nullable|string|max:255',
            'street'   => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'zip'      => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        $user->update($request->only([
            'name', 'email', 'phone', 'company', 'street', 'city', 'state', 'zip'
        ]));

        return response()->json(['message' => 'Profile updated successfully']);
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