<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($req, Closure $next)
    {
        $version=Settings()!=null ? (int) Settings()->version : 0;
        $url=Settings()!=null ? Settings()->app_url : 'Contact to Admin';

        $req_version=(int) $req->header('version');

        if($req_version!=$version){
                return response()->json(['success'=>true,'status' => 'Please update your app to following version: v'.$version, 'url'=>$url, 'error_code'=>403], 200);
        }

        try
        {
            $user = JWTAuth::parseToken()->authenticate();
        }
        catch(Exception $e){

            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException)
                {
                return response()->json(['status' => 'Token is Invalid'], 401);
                }
            else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException)
                {
                return response()->json(['status' => 'Token is Expired'], 401);
                }
            else
                {
                    return response()->json(['status' => 'Authorization Token not found'], 401);
                }
        }
        return $next($req);
    }
}

