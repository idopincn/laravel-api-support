<?php

namespace Idopin\ApiSupport\Middleware;

use Closure;
use Idopin\ApiSupport\Enums\ApiCode;
use Idopin\ApiSupport\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Human
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $valid_key = $request->input('valid_key');
        $valid_code = $request->input('valid_code');

        // 前置处理

        if (!$code = Cache::get($valid_key)) {
            return $this->response(ApiCode::CAPTCHA_INVALID);
        }

        if (!hash_equals(strtolower((string)$code), strtolower($valid_code))) {
            return $this->response(ApiCode::CAPTCHA_INCORRECT);
        }

        // 后置处理
        $response =  $next($request);

        if ($this->responseEqual($response, ApiCode::RESOURCE_CREATED)) {
            Cache::forget($valid_key);
        }

        return $response;
    }
}
