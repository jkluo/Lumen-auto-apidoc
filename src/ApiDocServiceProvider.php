<?php

namespace Jkluo\LumenApiDoc;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Routing\Router;

class ApiDocServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    protected $defer = false;
    public function boot()
    {
        $viewPath = __DIR__.'/../views';
        $this->loadViewsFrom($viewPath, 'apidoc');
        $this->setupRoutes($this->app->router);
    }

    /**
     * Define the routes for the application.
     *
     * @param $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group([
            'namespace' => 'Jkluo\LumenApiDoc\Http\Controllers',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('apidoc',function($app){
            return new Contact($app);
        });
        $this->app->configure('doc');
    }
}
