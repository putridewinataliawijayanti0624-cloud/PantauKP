<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Akun yang dinonaktifkan admin langsung dikeluarkan pada request berikutnya. */
class EnsureActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi administrator.']);
        }

        return $next($request);
    }
}
