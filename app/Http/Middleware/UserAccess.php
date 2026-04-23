<?php
  
namespace App\Http\Middleware;
  
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $userType): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Normalize user type and allow access when it matches route middleware parameter.
        $typeMap = [
            'admin' => '1',
            'user' => '0',
            'api' => '2',
        ];

        $requiredType = $typeMap[(string) $userType] ?? null;
        $currentType = (string) $user->type;

        if ($requiredType !== null && $currentType === $requiredType) {
            return $next($request);
        }

        // Redirect authenticated users to the dashboard that matches their role.
        if ($currentType === '1') {
            return redirect()->route('admin.dashboard');
        } elseif ($currentType === '0') {
            return redirect()->route('user.dashboard');
        } elseif ($currentType === '2' && \Illuminate\Support\Facades\Route::has('api.home')) {
            return redirect()->route('api.home');
        }

        // Unknown type: force a clean login instead of redirecting to /dashboard (loop risk).
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Your account role is not configured. Please contact support.');
    }

}

