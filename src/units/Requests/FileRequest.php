<?php

namespace Idopin\ApiSupport\Requests;

use Idopin\ApiSupport\Requests\FormRequest;

class FileRequest extends FormRequest{
    public function rules()
    {
        return [
            'file' => 'required|file',
            'public' =>'in:0,1|nullable'
        ];
    }
}