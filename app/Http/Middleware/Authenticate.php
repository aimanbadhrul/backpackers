<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        
        if (Session::has('impersonate')) {
            $impersonatedUserId = Session::get('impersonate');
            Auth::loginUsingId($impersonatedUserId);
        }

        return $next($request);
    }

    protected function redirectTo($request)
{
    // dd('test');
    if (! $request->expectsJson()) {
        return route('admin/login'); // or just return 'admin/login';
    }
}

}
