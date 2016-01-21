<?php

namespace Evaneos\REST\Kernel;

use Silex\Application;

interface KernelInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getEnv();

    /**
     * @return bool
     */
    public function isDebug();

    /**
     * @param bool $id
     *
     * @return int|string
     */
    public function getVersion($id = false);

    /**
     * @return Application
     */
    public function getApp();

    public function boot();
}
