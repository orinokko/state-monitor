<?php
namespace Orinoko\StateMonitor\Test;

use Monitor;

class TestTest extends TestCase
{
    /**
     * Check that the multiply method returns correct result
     * @return void
     */
    public function testMultiplyReturnsCorrectValue()
    {
        $this->assertSame(Monitor::checkFacade(4, 4), 16);
    }
}