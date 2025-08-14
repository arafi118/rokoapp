<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnggotaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->jabatan == '2') {
            return redirect('/inspeksi');
        }

        if (auth()->user()->jabatan == '3') {
            return redirect('/mandor');
        }

        return $next($request);
    }
}
