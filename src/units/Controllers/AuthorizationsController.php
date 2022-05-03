<?php

namespace Idopin\ApiSupport\Controllers;

use Idopin\ApiSupport\Controller;
use Idopin\ApiSupport\Enums\ApiCode;
use Illuminate\Support\Facades\Http;
use Idopin\ApiSupport\Requests\AuthorizationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthorizationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['id', 'status', 'store']);
    }

    /**
     * 获取用户ID
     *
     * @return JsonResponse
     */
    public function id(): JsonResponse
    {
        return  auth('api')->check()
            ? $this->response(ApiCode::OK, auth('api')->id())
            : $this->response();
    }


    /**
     * 用户登录
     *
     * @param AuthorizationRequest $request
     * @return JsonResponse
     */
    public function store(AuthorizationRequest $request): JsonResponse
    {
        // 此处会抛出异常
        // $captcha_key =  checkCaptcha($request->captcha_key, $request->captcha_code);

        $response =  Http::post(config('app.url') . '/oauth/token', $this->__passport_client($request->username, $request->password));

        $result = $response->json();


        /*
        error的可能值

        errors:
        - invalid_grant
        - unsupported_grant_type
        - invalid_client

        */
        if (array_key_exists('error', $result)) {
            if (hash_equals('invalid_grant', $result['error'])) {
                return $this->response(ApiCode::USER_AUTH_FAILED);
            } else {
                return $this->response(ApiCode::UNDEFINE, null, $result['message']);
            }
        }

        // \Cache::forget($captcha_key);
        return $this->response(ApiCode::RESOURCE_CREATED, $result);
    }

    /**
     * 注销登录
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
            return $this->response(ApiCode::NO_CONTENT);
        } else {
            return $this->response(ApiCode::TOKEN_INVALID);
        }
    }


    private function __passport_client(string $username, string $passport)
    {
        $client = DB::table('oauth_clients')->where('name', 'password_client')->first();

        /*

        json:
        {
            "username" : "xxx",
            "password" : "xxx",
            "grant_type": "password",
            "client_id" :  "xxx",
            "client_secret":"xxx",
            "scope" : "*"
        }

        */

        return [
            'username' => $username,
            'password' => $passport,
            'grant_type' => 'password',
            'client_id' =>  $client->id,
            'client_secret' => $client->secret,
            'scope' => '*'
        ];
    }
}
