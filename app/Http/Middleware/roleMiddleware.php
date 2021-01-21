<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\AccessoController;

class roleMiddleware
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

        if(Auth::user()->role < 5){
            return $next($request);
        }

        return redirect()->action([AccessoController::class, 'getLogin']);
    }
}
