<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminEtProfMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role == 1 || auth()->user()->role == 0) {
            return $next($request);
        } else {
            return redirect('dashboard')->with(['error' => 'Vous devez être professeur ou administrateur']);
        }
    }
}
