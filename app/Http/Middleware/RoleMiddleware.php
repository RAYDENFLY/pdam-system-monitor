<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('dashboard')->with('error', 'Silakan login terlebih dahulu!');
        }

        // **Admin bisa akses semua halaman**
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // **Cek apakah role user ada dalam daftar role yang diizinkan**
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }

        return $next($request);
    }
}
