<?php

namespace Idopin\ApiSupport\Providers;

use Idopin\ApiSupport\Commands\Authorization;
use Laravel\Passport\Passport;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        // 扩展包默认配置
        $this->mergeConfigFrom(__DIR__ . './../../config/api_responses.php', 'responses');

        $this->mergeConfigFrom(__DIR__ . './../../config/user.php', 'user');

        $this->mergeConfigFrom(__DIR__ . './../../config/easysms.php','easysms');
    }

    public function boot()
    {

        // 发布配置文件
        $this->publishes([
            // __DIR__ . '/../config/api_responses.php' => config_path('api_responses.php'),
        ]);

        Passport::routes();

        $this->loadRoutesFrom(__DIR__ . '/' . '../../routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Authorization::class
            ]);
        }
    }
}
