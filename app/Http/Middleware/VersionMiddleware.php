<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $req, Closure $next)
    {
        $version=Settings()!=null ? (int) Settings()->version : 0;
        $url=Settings()!=null ? Settings()->app_url : 'Contact to Admin';

        $req_version=(int) $req->header('version');

        if($req_version!=$version){
                return response()->json(['status' => 'Please update your app to following version: v'.$version, 'url'=>$url, 'error_code'=>403], 403);
        }

        
        return $next($request);
    }
}
