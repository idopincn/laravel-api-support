<?php

return [
    'username' => env('RULE_USERNAME', 'required|between:2,20|regex:/^[A-Za-z0-9\-\_]+$/'),
    'password' => env('RULE_PASSWORD', 'required|alpha_dash|min:8')
];
