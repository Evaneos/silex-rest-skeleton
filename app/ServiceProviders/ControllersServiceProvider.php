<?php

namespace Evaneos\REST\ServiceProviders;

use Evaneos\REST\API\Controllers\ApiController;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllersServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['application.controllers.api'] = $app->share(function () use ($app) {
            return new ApiController($app['api.response.builder']);
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
