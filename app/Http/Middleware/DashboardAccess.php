<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || 
            !in_array(Auth::user()->role, ['admin', 'teknisi', 'kasir'])) {
            return redirect('http://127.0.0.1:8000');
        }

        return $next($request);
    }
}