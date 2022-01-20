<?php

namespace App\Providers;

use App\Http\Middleware\Roles;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Auth\Guard\KeycloakGuard;
use App\Auth\WebUserProvider;
use App\Http\Middleware\Authenticate;
use App\Services\KeycloakService;

class KeycloakServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // User Provider
        Auth::provider('keycloak-users', function($app, array $config) {
            return new WebUserProvider($config['model']);
        });

        // Gate
        Gate::define('keycloak', function ($user, $roles, $resource = '') {
            return $user->hasRole($roles, $resource) ?: null;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Keycloak Web Guard
        Auth::extend('keycloak', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            return new KeycloakGuard($provider, $app->request);
        });

        // Facades
        $this->app->bind('keycloak', function($app) {
            return $app->make(KeycloakService::class);
        });

        // Routes
        $this->registerRoutes();

        // Middleware Group
        $this->app['router']->middlewareGroup('keycloak', [
            StartSession::class,
            Authenticate::class,
        ]);

        // Add Middleware "permissions"
        $this->app['router']->aliasMiddleware('roles', Roles::class);

        // Bind for client data
        $this->app->when(KeycloakService::class)->needs(ClientInterface::class)->give(function() {
            return new Client(Config::get('keycloak.guzzle_options', []));
        });
    }

    /**
     * Register the authentication routes for keycloak.
     *
     * @return void
     */
    private function registerRoutes()
    {
        $routes = Config::get('keycloak.routes', []);

        // Register Routes
        $router = $this->app->make('router');

        if (!empty($routes['login'])) $router->middleware('web')->get($routes['login'], 'App\Http\Controllers\AuthController@login')->name('keycloak.login');
        if (!empty($routes['logout'])) $router->middleware('web')->get($routes['logout'], 'App\Http\Controllers\AuthController@logout')->name('keycloak.logout');
        if (!empty($routes['register'])) $router->middleware('web')->get($routes['register'], 'App\Http\Controllers\AuthController@register')->name('keycloak.register');

        $router->middleware('web')->get('callback', 'App\Http\Controllers\AuthController@callback')->name('keycloak.callback');
    }
}
