<?php

namespace Evaneos\REST\CLI;

use Evaneos\REST\Kernel\Kernel;
use Evaneos\REST\Kernel\KernelInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    /** @var KernelInterface  */
    private $kernel;

    /**
     * Application constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct($kernel->getName(), Kernel::VERSION . ' - ' . $kernel->getName() . '/' . $kernel->getEnv() . ($kernel->isDebug() ? '/debug' : ''));

        $kernel->boot();

        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnv()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));

        $this->setDispatcher($kernel->getApp()->offsetGet('dispatcher'));

        $registeredCommand = array_reduce($kernel->getApp()->keys(), function (&$res, $serviceName) {
            if ('command.' === substr($serviceName, 0, 8)) {
                $res[] = $serviceName;

                return $res;
            }
        });

        foreach ($registeredCommand as $serviceName) {
            $this->add($kernel->getApp()->offsetGet($serviceName));
        }
    }

    /**
     * @return KernelInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }
}
