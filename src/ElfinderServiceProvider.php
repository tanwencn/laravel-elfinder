<?php
/**
 * http://www.tanecn.com
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2018/10/12 11:02
 */

namespace Tanwencn\Elfinder;

use Illuminate\Support\ServiceProvider;

class ElfinderServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config' => config_path(),
                __DIR__ . '/../resources/assets' => public_path('vendor/laravel-elfinder'),
            ], 'laravel-elfinder');
        }

        $this->loadRoutesFrom(__DIR__ . '/router.php');
        
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'elfinder');

    }
}
