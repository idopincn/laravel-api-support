<?php

use Idopin\ApiSupport\Enums\ApiCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Idopin\ApiSupport\Requests\FormRequest;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Json 内容响应
 *
 * @param Idopin\ApiSupport\Enums\ApiCode $returnCode 返回码
 * @param mixed $result 结果
 * @param null|string|null $message 返回的信息，会覆盖默认的信息
 * @param integer|null $httpCode  HTTP 响应码
 * @return JsonResponse
 */
function api_response(ApiCode $apiCode = ApiCode::OK, $result = null, string $message = '', int $httpCode = null): JsonResponse
{

    $codeData = config('responses.' . $apiCode->value);

    $httpCode = $httpCode ?: $codeData[2];

    return response()->json([
        'http_status' => $httpCode,
        'code' => $codeData[0],
        'message' => $message ?: $codeData[1],
        'result' => $result
    ], $httpCode);
}

/**
 * 对比响应
 *
 * @param \Illuminate\Http\JsonResponse $response
 * @param \Idopin\ApiSupport\Enums\ApiCode $apiCode
 * @return boolean
 */
function api_response_equal(JsonResponse $response, ApiCode $apiCode): bool
{
    $code = config('responses.' . $apiCode->value)[0];
    return json_decode($response->content())->code === $code;
}


/**
 * 发送验证码
 *
 * @param string $type 验证码类型： captcha;sms
 * @param FormRequest|null|null $request
 * @return void
 */
function send_code(string $type = 'captcha', FormRequest|null $request = null)
{
    if ($type === 'captcha') {

        $phraseBuilder = new PhraseBuilder(4);

        $captcha = new CaptchaBuilder(null, $phraseBuilder);
        $captcha->build(90, 32);

        $base64 = $captcha->inline();
        $code = $captcha->getPhrase();

        $verify_key = time() . '_' . Str::random(16);

        Cache::put($verify_key, $code, now()->addMinutes(5));

        return api_response(ApiCode::OK, [
            'verify_key' => $verify_key,
            'verify_code' => $base64
        ]);
    } else {
        if (!$request) {
            return api_response(ApiCode::FORM_DATA_INVALID, null, '手机号码不能为空');
        }

        // 限流

        $key = 'send-message:' . $request->fingerprint();

        // 请求过多返回 false
        $res = RateLimiter::attempt(
            $key,
            1, // 次数
            function () use ($request) {
                $easySms = new EasySms(config('easysms'));

                $verify_key = time() . Str::random(4) . '_' . $request->phone;
                $code = '';

                if (!app()->environment('production')) {
                    $code = '123456';
                } else {
                    // 验证码， 6位随机㶼
                    // 生成4位随机数，左侧补0
                    $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT);

                    try {
                        $easySms->send($request->phone, [
                            'template' => config('easysms.gateways.aliyun.templates.register'),
                            'data' => [
                                'code' => $code
                            ],
                        ]);
                    } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                        $message = $exception->getException('aliyun')->getMessage();
                        abort(500, $message ?: '短信发送异常');
                    }
                }

                Cache::put($verify_key, $code, now()->addMinutes(5));

                return api_response(ApiCode::OK, [
                    'verify_key' => $verify_key,
                ]);
            },
            30  // 时间：秒
        );

        if (!$res) {
            $second = RateLimiter::availableIn($key);
            return api_response(ApiCode::ATTEMPT_TO_MANY, null, "请求短信过于频繁,请在 {$second} 秒后再试");
        }

        return $res;
    }
}
