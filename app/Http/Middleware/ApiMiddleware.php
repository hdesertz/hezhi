<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Closure;
use Log;

class ApiMiddleware
{


//    protected $auth;

//    protected $route_without_auth = ['callback','sms', 'api\/documentation', 'version','sign','agreement','PushMessage','cib\/interest','newsStates','sms3RD'];

//    protected $route_without_token = ['user\/login', 'user\/register','user','bills','point'];


    public function handle($request, Closure $next)
    {

        return $next($request);

        foreach ($this->route_without_auth as $route) {
            if (preg_match("/$route/", $request->path())) {
                return $next($request);
            }
        }

        if (!$request->header('timestamp')) {
            return BaseController::sendError(4001);
        }
        $need_token = true;
        foreach ($this->route_without_token as $route) {
            if (preg_match("/$route/", $request->path())) {
                $need_token = false;
            }
        }

        if ($need_token && !$request->header('token')) {
            return BaseController::sendError(4005);
        }

        //异地登陆
        $token = Customer::where('token', $request->header('token'))->first();
        if($need_token && is_null($token)){
            return BaseController::sendError(4006);
        }

        //未认证
        if($need_token && $token->state == 0){
            return BaseController::sendError(4007);
        }

        if ($need_token && !is_null($token) && $token['token'] == '') {
            return BaseController::sendError(4002);
        }

        if (!$request->header('api-key') || $request->header('api-key') != env('API_KEY')) {
            return BaseController::sendError(4003);
        }

        if (!self::signVerify($request)) {
            return BaseController::sendError(4004);
        }


        return $next($request);
    }


    private static function signVerify($request)
    {
        $sign = $request->header('sign');
        $params['token'] = $request->header('token', '');
        $params['api-key'] = $request->header('api-key');
        $params['timestamp'] = $request->header('timestamp');
        $params['api-secret'] = env('API_SECRET');
        ksort($params);

        $plain = http_build_query($params);
        //Log::info(print_r($plain, true));
        //Log::info("sign: " . md5($plain));
        return md5($plain) == $sign;
    }


}
