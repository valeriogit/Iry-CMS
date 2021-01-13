<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\AccessoController;

class isAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()){ return redirect()->route('login'); }

        if(Auth::user()->isSuperAdmin()){
            return $next($request);
        }

        return redirect()->action([AccessoController::class, 'getLogin']);
    }
}
