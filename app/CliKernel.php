<?php

namespace Evaneos\REST;

use Evaneos\REST\Kernel\Kernel;
use Evaneos\REST\ServiceProviders\CommandServiceProvider;

class CliKernel extends Kernel
{
    /**
     * @throws \Exception
     */
    protected function doBoot()
    {
        $this->app->register(new CommandServiceProvider());
    }
}
