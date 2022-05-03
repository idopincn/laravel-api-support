<?php

/* 一、没有错误 */
/* 二、验证码 */
/* 三、用户认证 */
/* 四、用户授权 */
/* 五、数据库 */
/* 六、Token */
/* 七、表单验证 */
/* 八、限流 */
/* 九、资源没找到 */
/* 待定错误 */


return [
    'ok'                           => [0, 'OK', 200],
    'no_content'                   => [1, '无返回内容', 204],
    'redirect'                     => [2, '重定向', 301],

    'resource_created'             => [100, '创建新资源', 201],
    'resource_updated'             => [101, '更新了资源', 200],
    'resource_exist'               => [102, '资源已存在', 200],
    'resource_create_failed'       => [103, '资源创建失败', 200],
    'resource_update_failed'       => [104, '资源更新失败', 200],
    'resource_not_found'           => [900, '资源不存在', 404],

    'captcha_exception'            => [200, '验证码异常', 403],
    'captcha_invalid'              => [201, '验证码失效', 403],
    'captcha_incorrect'            => [202, '验证码不正确', 403],
    'sms_code_exception'           => [203, '短信验证码异常', 403],
    'sms_code_invalid'             => [204, '短信验证码失效', 403],
    'sms_code_incorrect'           => [205, '短信验证码不正确', 403],
    'verification_code_exception'  => [206, '验证码异常', 403],
    'verification_code_invalid'    => [207, '验证码失效', 403],
    'verification_code_incorrect'  => [208, '验证码不正确', 403],

    'user_not_exist'               => [300, '用户不存在', 404],
    'user_auth_failed'             => [302, '用户认证失败，用户名或密码不正确', 401],
    'user_create_failed'           => [305, '用户创建失败', 403],
    'user_not_auth'                => [306, '用户未登录', 401],

    'database_query'               => [500, '数据库操作失败', 500],

    'token_invalid'                => [601, 'Token 已失效', 403],

    'form_data_invalid'            => [700, '表单有字段错误', 422],

    'attempt_to_many'              => [800, '请求次数过多', 429],

    'route_not_found'              => [900, '路由不存在', 404],

    'undefine'                     => [9999, '未定义的错误', 500],
];
