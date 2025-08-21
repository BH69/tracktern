<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has the required role
        if ($user->role !== $role) {
            // Redirect to appropriate dashboard based on user's actual role
            if ($user->isAdmin()) {
                return redirect()->route('coordinators.dashboard')
                    ->with('error', 'Access denied. You are being redirected to your dashboard.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Access denied. You are being redirected to your dashboard.');
            }
            
            // Fallback if role is not recognized
            return redirect()->route('login')
                ->with('error', 'Access denied. Invalid user role.');
        }

        return $next($request);
    }
}
