<?php

namespace Idopin\ApiSupport\Requests;

use Idopin\ApiSupport\Requests\FormRequest;

class SmsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|phone:CN,mobile|unique:users',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '电话号码不能为空',
            'phone.phone' => '电话号码格式不正确',
            'phone.unique' => '此电话号码已被注册'
        ];
    }
}
