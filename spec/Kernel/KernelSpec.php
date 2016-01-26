<?php

namespace spec\Evaneos\REST\Kernel;

use Evaneos\REST\Kernel\Kernel;
use Evaneos\REST\Kernel\KernelInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Silex\Application;
use Silex\ServiceProviderInterface;

class KernelSpec extends ObjectBehavior
{
    protected $rootDir;

    function let()
    {
        $this->beConstructedWith('dev', true);
        $this->rootDir = preg_replace("/spec/i", "app", __DIR__.'/../..');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Kernel::class);
    }

    function it_should_implement_kernel_interface()
    {
        $this->shouldHaveType(KernelInterface::class);
    }

    function it_should_have_env()
    {
        $this->getEnv()->shouldReturn('dev');
        $this->getApp()
            ->offsetGet('env')
            ->shouldReturn('dev')
        ;
    }

    function it_should_have_debug()
    {
        $this->isDebug()->shouldReturn(true);
        $this->getApp()
            ->offsetGet('debug')
            ->shouldReturn(true)
        ;
    }

    function it_should_have_silex_app()
    {
        $this->getApp()->shouldBeAnInstanceOf(Application::class);
    }

    function it_should_have_root_dir()
    {
        $this->getApp()
            ->offsetGet('root_dir')
            ->shouldReturn($this->rootDir)
        ;
    }

    function it_should_have_log_dir()
    {
        $this->getApp()
            ->offsetGet('log_dir')
            ->shouldReturn($this->rootDir.'/log')
        ;
    }

    function it_should_have_cache_dir()
    {
        $this->getApp()
            ->offsetGet('cache_dir')
            ->shouldReturn($this->rootDir.'/cache')
        ;
    }

    function it_should_have_version()
    {
        $this->getVersion()->shouldReturn('1.0.0');
    }

    function it_should_have_name()
    {
        $this->getName()->shouldReturn('Silex Skeleton');
    }

    function it_should_not_reboot()
    {
        $this->boot();

        $this->boot();
    }
}
