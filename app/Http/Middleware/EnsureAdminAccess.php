<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->boolean('madeflix_admin_authenticated')) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
