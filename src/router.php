<?php

Route::middleware('web')->prefix('elfinder')->namespace('Tanwencn\Elfinder')->group(function ($router) {
        $router->any('connector', ['as' => 'elfinder.connector', 'uses' => 'Controller@showConnector']);
        $router->get('show', ['as' => 'admin.elfinder.show', 'uses' => 'Controller@showIndex']);
    });

