<?php

namespace Evaneos\REST\ServiceProviders;

use Evaneos\REST\API\Controllers\ApiController;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class ControllersServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['application.controllers.api'] = function () use ($app) {
            return new ApiController($app['api.response.builder']);
        };
    }
}
