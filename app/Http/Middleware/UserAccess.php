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
        $requiredRole = strtolower((string) $userType);
        $currentRole = $this->resolveUserRole($user);

        if ($currentRole !== null && $requiredRole === $currentRole) {
            return $next($request);
        }

        // Redirect authenticated users to the dashboard that matches their role.
        if ($currentRole === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($currentRole === 'user') {
            return redirect()->route('user.dashboard');
        } elseif ($currentRole === 'api' && \Illuminate\Support\Facades\Route::has('api.home')) {
            return redirect()->route('api.home');
        }

        // Unknown type: force a clean login instead of redirecting to /dashboard (loop risk).
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Your account role is not configured. Please contact support.');
    }

    protected function resolveUserRole(object $user): ?string
    {
        $rawType = (string) ($user->getRawOriginal('type') ?? '');
        $displayType = strtolower((string) ($user->type ?? ''));

        $map = [
            '0' => 'user',
            'user' => 'user',
            '1' => 'admin',
            'admin' => 'admin',
            '2' => 'api',
            'api' => 'api',
            'manager' => 'api',
        ];

        if (isset($map[$rawType])) {
            return $map[$rawType];
        }

        return $map[$displayType] ?? null;
    }

}

