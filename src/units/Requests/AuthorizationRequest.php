<?php

namespace Idopin\ApiSupport\Requests;

use Idopin\ApiSupport\Requests\FormRequest;

class AuthorizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' =>config('user.username'),
            'password' => config('user.password'),
        ];
    }
}
