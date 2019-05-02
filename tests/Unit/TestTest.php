<?php
namespace Orinoko\StateMonitor\Tests\Unit;

use Monitor;
use Orinoko\StateMonitor\Tests\TestCase;

class TestTest extends TestCase
{
    /**
     * Check that the multiply method returns correct result
     * @return void
     */
    public function testMultiplyReturnsCorrectValue()
    {
        $this->assertSame(Monitor::checkFacade(), 'facade run');
        $this->assertSame(Monitor::checkFacade(), 'nope');
    }
}