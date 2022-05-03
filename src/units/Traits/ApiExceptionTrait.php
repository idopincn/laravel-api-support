<?php

namespace Idopin\ApiSupport\Traits;

use Idopin\ApiSupport\Enums\ApiCode;
use Throwable;

trait ApiExceptionTrait
{
    use ApiResponseTrait;
    public function jsonResponse(Throwable $e)
    {
        if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            $msg  = $e->getMessage();
            return $this->response(ApiCode::ATTEMPT_TO_MANY, null, $msg);
        }


        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $model = $e->getModel();
            if ($model === User::class) {
                return $this->response(ApiCode::USER_NOT_EXIST);
            } else {
                return $this->response(ApiCode::RESOURCE_NOT_FOUND, $e->getMessage());
            }
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response($e);
        }

        if ($e instanceof \Illuminate\Database\QueryException) {
            return $this->response(ApiCode::DATABASE_QUERY, env('APP_DEBUG') ? $e->getMessage() : null);
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->response(ApiCode::RESOURCE_NOT_FOUND, $e->getMessage());
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->response(ApiCode::USER_NOT_AUTH, $e->getMessage());
        }

        if ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException) {
            return $this->response(ApiCode::ROUTE_NOT_FOUND, $e->getMessage());
        }

        return $this->response(ApiCode::UNDEFINE, $e->getMessage() . ' class:' . get_class($e));
    }
}
