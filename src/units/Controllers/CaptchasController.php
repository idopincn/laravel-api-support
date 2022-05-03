<?php

namespace Idopin\ApiSupport\Controllers;

use Idopin\ApiSupport\Controller;

class CaptchasController extends Controller  {

    public function store()
    {
       return send_code();
    }
}