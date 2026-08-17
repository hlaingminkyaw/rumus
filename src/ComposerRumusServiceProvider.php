<?php

namespace ComposerRumus;

use Illuminate\Support\ServiceProvider;

class ComposerRumusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/composer-rumus.php',
            'composer-rumus'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'composer-rumus');

        $this->publishes([
            __DIR__.'/../config/composer-rumus.php' => config_path('composer-rumus.php'),
        ], 'composer-rumus-config');
    }
}