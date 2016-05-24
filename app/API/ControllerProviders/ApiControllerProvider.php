<?php

namespace Evaneos\REST\API\ControllerProviders;

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

        $controllers->get(
            '/',
            'application.controllers.api:root'
        );

        return $controllers;
    }
}
