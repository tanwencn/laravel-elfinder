<?php
/**
 * http://www.tanecn.com
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2018/10/12 11:02
 */

namespace Tanwencn\Elfinder;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Tanwencn\Admin\Consoles\BootPermissionsCommand;
use Tanwencn\Admin\Consoles\BuildDirCommand;
use Tanwencn\Admin\Consoles\InstallCommand;
use Tanwencn\Admin\Foundation\Admin;
use Tanwencn\Admin\Http\BootstrapComposer;

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
