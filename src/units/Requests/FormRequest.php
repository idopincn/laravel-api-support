<?php

namespace Idopin\ApiSupport\Requests;

use Idopin\ApiSupport\Enums\ApiCode;
use Idopin\ApiSupport\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


//use Illuminate\Http\Request;


class FormRequest extends BaseFormRequest
{
    use ApiResponseTrait;
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
            //
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errormsg = $validator->errors()->messages();
        foreach ($errormsg  as $item) {
            $msg = current($item);
            break;
        };

        throw (new HttpResponseException($this->response(ApiCode::FORM_DATA_INVALID, null, $msg)));
    }
}
