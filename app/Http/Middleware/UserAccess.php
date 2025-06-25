<?php
  
namespace App\Http\Middleware;
  
use Closure;
use Illuminate\Http\Request;
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

    //    dd($user->type);

        if (request()->routeIs('admin.dashboard') && $user->type == '1') {
            return $next($request); // don't redirect if already here
        }

        if (request()->routeIs('user.dashboard') && $user->type == '0') {
            return $next($request);
        }

        if (request()->routeIs('api.dashboard') && $user->type == '2') {
            return $next($request);
        }


        // Redirect if not already at the destination
        if ($user->type == '1') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->type == '0') {
            return redirect()->route('user.dashboard');
        }elseif ($user->type == '2') {
            return redirect()->route('api.dashboard');
        }

        return redirect('/dashboard');
    }

}

