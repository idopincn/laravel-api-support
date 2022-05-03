<?php

namespace Idopin\ApiSupport\Resources;

use Illuminate\Http\Resources\Json\JsonResource as HttpJsonResource;

class JsonResource extends HttpJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public static $wrap  = 'result';

    public function with($request)
    {
        return [
            'http_status' => 200,
            'code' => 0,
            'message' => 'OK'
        ];
    }
}
