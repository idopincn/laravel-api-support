<?php

namespace Idopin\ApiSupport\Traits;

use Idopin\ApiSupport\Enums\ApiCode;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Json 内容响应
     *
     * @param Idopin\ApiSupport\Enums\ApiCode $returnCode 返回码
     * @param mixed $result 结果
     * @param null|string|null $message 返回的信息，会覆盖默认的信息
     * @param integer|null $httpCode  HTTP 响应码
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(ApiCode $apiCode = ApiCode::OK, $result = null, string $message = '', int $httpCode = null): JsonResponse
    {
        return api_response($apiCode, $result, $message, $httpCode);
    }


    /**
     * 对比响应
     *
     * @param Illuminate\Http\JsonResponse $response
     * @param Idopin\ApiSupport\Enums\ApiCode $apiCode
     * @return boolean
     */
    public function responseEqual(JsonResponse $response, ApiCode $apiCode) :bool
    {
        $code = config('responses.' . $apiCode->value)[0];
        return json_decode($response->content())->code === $code;
    }
}
