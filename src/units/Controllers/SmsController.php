<?php

namespace Idopin\ApiSupport\Controllers;

use Idopin\ApiSupport\Controller;
use Idopin\ApiSupport\Requests\SmsRequest;

class SmsController extends Controller
{

    public function store(SmsRequest $request)
    {
        return send_code('sms', $request);
    }
}
