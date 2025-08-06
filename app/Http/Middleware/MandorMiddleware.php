<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MandorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->level_aktif->level->nama == 'inspeksi') {
            return redirect('/inspeksi');
        }

        if (auth()->user()->level_aktif->level->nama == 'mandor') {
            return $next($request);
        }

        return redirect('/anggota');
    }
}
