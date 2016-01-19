<?php

namespace Evaneos\REST\API\ControllerProviders;

use Evaneos\REST\API\Controllers\ApiController;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ApiControllerProvider implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $app['application.controllers.api'] = $app->share(function () use ($app) {
            return new ApiController($app['api.response.builder']);
        });

        $controllers->get('/', 'application.controllers.api:root');

        return $controllers;
    }
}
