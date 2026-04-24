<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    /**
     * Create a new controller instance.
     *
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        return $this->authenticateWithEmailPassword($request, 'login');
    }

    public function showDeveloperLoginForm()
    {
        return view('auth.developer-login');
    }

    public function developerLogin(Request $request): RedirectResponse
    {
        return $this->authenticateWithEmailPassword($request, 'developer.login');
    }

    protected function authenticateWithEmailPassword(Request $request, string $failureRouteName): RedirectResponse
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! auth()->attempt(['email' => $input['email'], 'password' => $input['password']])) {
            return redirect()->route($failureRouteName)
                ->withInput($request->only('email'))
                ->with('error', 'Email-Address And Password Are Wrong.');
        }

        $user = auth()->user();
        $rawType = (string) ($user->getRawOriginal('type') ?? '');
        $displayType = strtolower((string) ($user->type ?? ''));

        $isAdmin = $rawType === '1' || $displayType === 'admin';
        $isApi = $rawType === '2' || in_array($displayType, ['api', 'manager'], true);

        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        }
        if ($isApi) {
            return redirect()->route('api.home');
        }

        return redirect()->route('user.dashboard');
    }
}
