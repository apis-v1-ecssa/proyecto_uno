<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapApiV1Routes();

        $this->mapApiV1PassportRoutes();

        $this->mapApiV1SecurityRoutes();

        $this->mapApiV1CatalogoRoutes();

        $this->mapApiV1PrincipalRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1Routes()
    {
        Route::prefix('api/v1/ecssa')
        ->middleware('api')
        ->namespace("{$this->namespace}\WEB\V1")
        ->group(base_path('routes/api_v1.php'));
    }

    /**
     * Define the "service/passport" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1PassportRoutes()
    {
        Route::prefix('service/passport')
        ->middleware('passport')
        ->group(base_path('routes/v1/api_servicio.php'));
    }

    /**
     * Define the "service/rest/v1/security" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1SecurityRoutes()
    {
        Route::prefix('service/rest/v1/security')
        ->middleware('security')
        ->namespace("{$this->namespace}\V1\Seguridad")
        ->group(base_path('routes/v1/api_seguridad.php'));
    }

    /**
     * Define the "service/rest/v1/catalogo" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1CatalogoRoutes()
    {
        Route::prefix('service/rest/v1/catalogo')
        ->middleware('catalogo')
        ->namespace("{$this->namespace}\V1\Catalogo")
        ->group(base_path('routes/v1/api_catalogo.php'));
    }

    /**
     * Define the "service/rest/v1/principal" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1PrincipalRoutes()
    {
        Route::prefix('service/rest/v1/principal')
        ->middleware('principal')
        ->namespace("{$this->namespace}\V1\Principal")
        ->group(base_path('routes/v1/api_principal.php'));
    }
}
