<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InspeksiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->jabatan == '2') {
            return $next($request);
        }

        if (auth()->user()->jabatan == '3') {
            return redirect('/mandor');
        }

        return redirect('/anggota');
    }
}
