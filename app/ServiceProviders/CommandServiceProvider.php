<?php

namespace Evaneos\REST\ServiceProviders;

use Evaneos\REST\Commands\SampleCommand;
use Evaneos\REST\Commands\ServerCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class CommandServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Container $app)
    {
        $app['command.default'] = function () {
            return new SampleCommand('command:sample');
        };

        $app['command.default'] = function () use ($app) {
            return new ServerCommand('server:command', $app);
        };
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
