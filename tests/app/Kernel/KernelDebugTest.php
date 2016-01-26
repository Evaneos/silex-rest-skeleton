<?php

namespace Evaneos\REST\Tests\app\Kernel;

use Evaneos\REST\Kernel\Kernel;

class KernelDebugTest extends AbstractKernel
{
    public function setUp()
    {
        parent::setUp();
        $this->kernel = new Kernel('test', true);
    }

    /**
     * @test
     */
    public function it_should_have_debug()
    {
        $this->assertEquals(true, $this->kernel->isDebug());
    }
}
