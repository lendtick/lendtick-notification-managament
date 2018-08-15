<?php


namespace App\Http\Middleware;

use Closure;
use App\Helpers\RestCurl; 
class TokenValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $token = $request->header('Authorization');
        try{
            if(!empty($token)){
                $this->doValidate($token);
            }else{
                throw new \Exception("Unauthorized.", 400);
            }
        }catch(\Exception $e){
            $status = 400; //$e->getCode() ? $e->getCode() : 500; 
            $arr['status'] = 0;
            $arr['data']   = '';
            $arr['message'] = $e->getMessage();
            return response()->json([$arr], $status);
        }
        return $next($request);

    }

    private function doValidate($token){
        $url = env('AUTH_URL').'auth/check'; 
        $response = RestCurl::exec('GET', $url, [], $token);
        if(empty($response['data']->data)){
             $msgError = $response['data']->message;
             $msgError = $msgError ? $msgError : "The token is invalid.";  
             throw new \Exception($msgError, $response['status']);
        }else{
            return TRUE;
        }
    }
}
