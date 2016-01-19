<?php

namespace Evaneos\REST\ServiceProviders;

use Evaneos\REST\Security\JWT\JWTAuthenticationProvider;
use Evaneos\REST\Security\JWT\JWTDecoder;
use Evaneos\REST\Security\JWT\JWTListener;
use Evaneos\REST\Security\JWT\SecurityUserConverter;
use Silex\Application;
use Silex\ServiceProviderInterface;

class SecurityJWTServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['security.authentication_listener.factory.jwt'] = $app->protect(function ($name, $options) use ($app) {

            $app['security.authentication_provider.' . $name . '.jwt'] = $app->share(function () use ($app, $options) {
                return new JWTAuthenticationProvider(new SecurityUserConverter(), new JWTDecoder($options['secret_key'], $options['allowed_algorithms']));
            });

            $app['security.authentication_listener.' . $name . '.jwt'] = $app->share(function () use ($app) {
                return new JWTListener($app['security.token_storage'], $app['security.authentication_manager']);
            });

            return array(
                'security.authentication_provider.' . $name . '.jwt',
                'security.authentication_listener.' . $name . '.jwt',
                null,
                'pre_auth',
            );
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
