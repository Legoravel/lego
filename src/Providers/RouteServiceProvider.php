<?php

namespace Lego\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;

abstract class RouteServiceProvider extends BaseServiceProvider
{
    /**
     * Read the routes from the "api.php" and "web.php" files of this Service
     */
    abstract public function map(Router $router);

    public function loadRoutesFiles($router, $namespace, $pathApi = null, $pathWeb = null): void
    {
        if (is_string($pathApi) && is_file($pathApi)) {
            $this->mapApiRoutes($router, $namespace, $pathApi);
        }
        if (is_string($pathWeb) && is_file($pathWeb)) {
            $this->mapWebRoutes($router, $namespace, $pathWeb);
        }
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     *
     * @return void
     */
    protected function mapApiRoutes($router, $namespace, $path, $prefix = 'api'): void
    {
        $router->group([
            'middleware' => 'api',
            'namespace' => $namespace,
            'prefix' => $prefix, // to allow the delete or change of api prefix
        ], function ($router) use ($path) {
            require $path;
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     *
     * @return void
     */
    protected function mapWebRoutes($router, $namespace, $path): void
    {
        $router->group([
            'middleware' => 'web',
            'namespace' => $namespace,
        ], function ($router) use ($path) {
            require $path;
        });
    }
}
