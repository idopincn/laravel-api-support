<h1 align="center"> Apisupport </h1>



### 一、安装

```shell
composer require idopin/api-support:^1.0
```

功能：

- 图片验证码
- 阿里短信验证码
- 路由守卫中间件
- 用户登录
- 文件上传与获取



### 二、配置



#### 2.1 异常处理

在文件 `app\Exceptions\Handler.php` 中添加以下方法：

```php
public function render($request, Throwable $e)
{
    if ($request->is('api/*')) {
        return $this->jsonResponse($e);
    }

    return  parent::render($request, $e);
}
```



#### 2.2 创建迁移

```sh
php artisan migrate
```

运行命令后，将会生成7个表：

1. oauth_access_tokens

2. oauth_auth_codes

3. oauth_clients

4. oauth_personal_access_clients

5. oauth_refresh_tokens

6. files

7. user_files



#### 2.3 执行 Aritsan 命令

```sh
php artisan authorization:install
```

运行此命令后，生成：

- 安全访问令牌加密秘钥；

    \storage\oauth-private.key

    \storage\oauth-public.key

- 访问令牌的 **个人访问客户端(id:1)** 和 两个**密码授权客户端(id:2，id3)**



#### 2.4. 添加 Trait

添加 `Laravel\PassportHasApiTokens` trait 到 `App\Models\User` 模型中，此 trait 提供一些帮助方法用于检查已认证用户的令牌和权限范围。



#### 2.5  配置文件

在配置文件`config/auth.php` 中，将 api 的授权看守器 guards 的 driver 设置为 passport。

```php
 'api' => [
            'driver' => 'passport',
            'provider' => 'users',
  ]
```



#### 2.6 .自定义用户名字段

当使用密码授权方式验证时，Passport 默认使用你的授权模型的 `email` 属性作为 “用户名”。当然，你也可以通过在你的模型中定义 `findForPassport` 方法来定义验证行为：

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        return $this->where('name', $username)->first();
    }
}
```



#### 2.7 自定义密码验证

当使用密码授权方式时，Passpot 默认使用模型的 `password` 属性验证密码。如果你的模型没有 `password` 属性，或者你想自定义密码验证逻辑，你可以在你的模型中自定义方法 `validateForPassportPasswordGrant`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
}
```

#### 2.8 添加环境变量

```sh
# aliyun 短信
SMS_ALIYUN_ACCESS_KEY_ID=
SMS_ALIYUN_ACCESS_KEY_SECRET=
SMS_ALIYUN_TEMPLATE_REGISTER=
SMS_ALIYUN_SIGNATURE=
```



### 三、使用

#### 3.1 返回状态码

```php
return [
    'ok'                           => [0, 'OK', 200],
    'redirect'                     => [1, '重定向', 301],
    'resource_created'             => [2, '创建新资源', 201],
    'resource_updated'             => [3, '更新了资源', 200],
    'resource_exist'               => [4, '资源已存在', 200],
    'resource_unchanged'           => [5, '资源无变化', 200],
    'no_content'                   => [6, '无返回内容', 204],
    'captcha_exception'            => [200, '验证码异常', 403],
    'captcha_invalid'              => [201, '验证码失效', 403],
    'captcha_incorrect'            => [202, '验证码不正确', 403],
    'sms_code_exception'           => [203, '短信验证码异常', 403],
    'sms_code_invalid'             => [204, '短信验证码失效', 403],
    'sms_code_incorrect'           => [205, '短信验证码不正确', 403],
    'verification_code_exception'  => [200, '验证码异常', 403],
    'verification_code_invalid'    => [201, '验证码失效', 403],
    'verification_code_incorrect'  => [202, '验证码不正确', 403],
    'user_not_exist'               => [300, '用户不存在', 404],
    'user_auth_failed'             => [302, '用户认证失败，用户名或密码不正确', 401],
    'user_create_failed'           => [305, '用户创建失败', 403],
    'user_not_auth'                => [306, '用户未登录', 403],
    'database_query'               => [500, '数据库操作失败', 500],
    'token_invalid'                => [601, 'Token 已失效', 403],
    'form_data_invalid'            => [700, '表单有字段错误', 422],
    'attempt_to_many'              => [800, '请求次数过多', 429],
    'resource_not_found'           => [900, '资源不存在', 404],
    'route_not_found'              => [1000, '路由不存在', 404],
    'undefine'                     => [9999, '未定义的错误', 500],
];
```

对应的枚举类型

`Idopin\ApiSupport\Enums\ApiCode`

```php
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
    case RESOURCE_CREATED             = 'resource_created';
    case RESOURCE_EXIST               = 'resource_exist';
    case RESOURCE_NOT_FOUND           = 'resource_not_found';
    case RESOURCE_UNCHANGED           = 'resource_unchanged';
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
```



#### 3.2 响应 Trait

ApiResponseTrait , 它主要提供了 `response` 方法  和 `responseEqual`  方法。

`Idopin\ApiSupport\Traits\ApiResponseTrait`

```php
     /**
     * Json 内容响应
     *
     * @param Idopin\ApiSupport\Enums\ApiCode $returnCode 返回码
     * @param mixed $result 结果
     * @param null|string|null $message 返回的信息，会覆盖默认的信息
     * @param integer|null $httpCode  HTTP 响应码
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(ApiCode $apiCode = ApiCode::OK, $result = null, string $message = '', int $httpCode = null): JsonResponse{
        ...
    }

    /**
     * 对比响应
     *
     * @param Illuminate\Http\JsonResponse $response
     * @param Idopin\ApiSupport\Enums\ApiCode $apiCode
     * @return boolean
     */
    public function responseEqual(JsonResponse $response, ApiCode $apiCode) :bool{
        ...
    }

    // 调用示例
    $this->responseEqual($response, ApiCode::OK)
```



#### 3.3 控制器基类

`Idopin\ApiSupport\Controller`

控制器基类引用了  响应 Trait，可直接通过 $this->response(...) 调用。



#### 3.4 辅助函数

```php
 /**
 * Json 内容响应
 *
 * @param Idopin\ApiSupport\Enums\ApiCode $returnCode 返回码
 * @param mixed $result 结果
 * @param null|string|null $message 返回的信息，会覆盖默认的信息
 * @param integer|null $httpCode  HTTP 响应码
 * @return JsonResponse
 */
function api_response(ApiCode $apiCode = ApiCode::OK, $result = null, string $message = '', int $httpCode = null): JsonResponse{
    ...
}

/**
 * 对比响应
 *
 * @param \Illuminate\Http\JsonResponse $response
 * @param \Idopin\ApiSupport\Enums\ApiCode $apiCode
 * @return boolean
 */
function api_response_equal(JsonResponse $response, ApiCode $apiCode): bool{
    ...
}


```



#### 3.5 表单验证基类

`Idopin\ApiSupport\Requests\FormRequest`

表单验证基类引用了  响应 Trait，可直接通过 $this->response(...) 调用。



#### 3.6 资源基类

`Idopin\ApiSupport\Resources\JsonResource`

响应内容包裹了在 result 里。

并带有以下三个字段：

- http_status
- code
- message



#### 3.7 异常 Trait

`Idopin\ApiSupport\Traits\trait ApiExceptionTrait`

```php
<?php

namespace Idopin\ApiSupport\Traits;

use Idopin\ApiSupport\Enums\ApiCode;
use Throwable;

trait ApiExceptionTrait
{
    use ApiResponseTrait;
    public function jsonResponse(Throwable $e)
    {
        ...
    }
    ...
}
```



#### 3.8 验证码中间件

```php
use Idopin\ApiSupport\Middleware\Human;
...
public function __construct()
{
    $this->middleware(Human::class)->only('xxx');
}
```





#### 3.9 路由

##### 3.9.1 用户认证

| URL               | 方法   | 数据                              | 路由名称              |
| :---------------- | ------ | --------------------------------- | --------------------- |
| authorizations    | POST   | username: string; password:string | authorizations.store  |
| authorizations/id | GET    | -                                 | authorizations.id     |
| authorizations    | DELETE | -                                 | authorizations.delete |



##### 3.9.2 文件上传

| URL           | 方法 | 数据                 | 路由名称 | 功能                       |
| :------------ | ---- | -------------------- | -------- | -------------------------- |
| /files/{file} | GET  | file:文件ID          |          | 根据文件 ID 显示文件       |
| /files        | POST | file:File;public:0/1 |          | 上传文件                   |
| /files/check  | POST | md5:String           |          | 根据文件ID判断文件是否存在 |



##### 3.9.3 图片与短信验证码

| URL           | 方法 | 数据                  | 路由名称 | 功能           |
| :------------ | ---- | --------------------- | -------- | -------------- |
| /api/captchas | POST |                       |          | 发送图片验证码 |
| /api/sms      | POST | phone:string 电话号码 |          | 发送短信验证码 |

