<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check direct role match
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Allow users with dosen data to access dosen routes
        if (in_array('dosen', $roles) && $user->canAccessDosenFeatures()) {
            return $next($request);
        }

        // Check if dosen has additional roles (dekan/kaprodi)
        if ($user->role === 'dosen') {
            // Allow dosen who is also dekan to access dekan routes
            if (in_array('dekan', $roles) && $user->isDekan()) {
                return $next($request);
            }
            
            // Allow dosen who is also kaprodi to access kaprodi routes
            if (in_array('kaprodi', $roles) && $user->isKaprodi()) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
