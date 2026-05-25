<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Wedding;

class EnsureUserHasWedding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pengecekan apakah user yang login punya Wedding
        if (! Wedding::where('user_id', auth()->id())->exists()) {
            // Jika tidak punya, redirect ke halaman Onboarding
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
