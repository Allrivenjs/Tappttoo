<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TattooArtist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('api')->user()->tattoo_artist()->exists()) {
            return response()->json(['message' => 'You are not a tattoo artist'], 403);
        }
        return $next($request);
    }
}
