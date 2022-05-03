<?php

namespace Idopin\ApiSupport\Enums;

enum ApiCode: string
{
    case ATTEMPT_TO_MANY              = 'attempt_to_many';
    case CAPTCHA_EXCEPTION            = 'captcha_exception';
    case CAPTCHA_INCORRECT            = 'captcha_incorrect';
    case CAPTCHA_INVALID              = 'captcha_invalid';
    case DATABASE_QUERY               = 'database_query';
    case FORM_DATA_INVALID            = 'form_data_invalid';
    case NO_CONTENT                   = 'no_content';
    case OK                           = 'ok';
    case REDIRECT                     = 'redirect';
    case RESOURCE_CREATE_FAILED       = 'resource_create_failed';
    case RESOURCE_CREATED             = 'resource_created';
    case RESOURCE_EXIST               = 'resource_exist';
    case RESOURCE_NOT_FOUND           = 'resource_not_found';
    case RESOURCE_UPDATE_FAILED       = 'resource_update_failed';
    case RESOURCE_UPDATED             = 'resource_updated';
    case ROUTE_NOT_FOUND              = 'route_not_found';
    case SMS_CODE_EXCEPTION           = 'sms_code_exception';
    case SMS_CODE_INCORRECT           = 'sms_code_incorrect';
    case SMS_CODE_INVALID             = 'sms_code_invalid';
    case TOKEN_INVALID                = 'token_invalid';
    case UNDEFINE                     = 'undefine';
    case USER_AUTH_FAILED             = 'user_auth_failed';
    case USER_CREATE_FAILED           = 'user_create_failed';
    case USER_NOT_AUTH                = 'user_not_auth';
    case USER_NOT_EXIST               = 'user_not_exist';
    case VERIFICATION_CODE_EXCEPTION  = 'verification_code_exception';
    case VERIFICATION_CODE_INCORRECT  = 'verification_code_incorrect';
    case VERIFICATION_CODE_INVALID    = 'verification_code_invalid';
}
