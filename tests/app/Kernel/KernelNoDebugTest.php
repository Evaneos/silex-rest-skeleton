<?php

namespace Evaneos\REST\Tests\app\Kernel;

use Evaneos\REST\Kernel\Kernel;

class KernelNoDebugTest extends AbstractKernel
{
    public function setUp()
    {
        parent::setUp();
        $this->kernel = new Kernel('test', false);
    }

    /**
     * @test
     */
    public function it_should_have_debug()
    {
        $this->assertEquals(false, $this->kernel->isDebug());
    }
}
