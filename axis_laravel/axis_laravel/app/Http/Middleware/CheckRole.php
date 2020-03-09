<?php

namespace App\Http\Middleware;
use Auth;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $roleid= ['admin'=>'1','user'=>'0'];
        $setrole=[];
        if(isset($roleid[$role])){
            $setrole[]=$roleid[$role];
        }
        $setrole= array_unique($setrole);
        if(Auth::check()){
            if(in_array(Auth::user()->isAdmin,$setrole)){
                return $next($request);
            }
            else{
                    return response()->json(['access denied']);
                }
        }

    }
    // public function handle($request, Closure $next)
    // {
    //     if(Auth::check()){
    //         if(Auth::user()->isAdmin == 1 ){
    //             return $next($request);
    //         }
    //         else{
    //                 return response()->json(['no access']);
    //         }
    //     }

    // }
}
