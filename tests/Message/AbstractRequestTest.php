<?php

namespace Omnipay\Przelewy24\Message;

use Mockery as m;
use Omnipay\Przelewy24\Message\AbstractRequest;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = m::mock(AbstractRequest::class)->makePartial();
        $this->request->initialize();
    }

    public function testTestModeIsFalsy()
    {
        $params = array('testMode' => 'false');

        $this->request->initialize($params);

        $this->assertFalse($this->request->testMode());
    }

    public function testTestModeIsFalsyWhenEmpty()
    {
        $params = array('testMode' => '');

        $this->request->initialize($params);

        $this->assertFalse($this->request->testMode());
    }

    public function testTestModeIsTruthy()
    {
        $params = array('testMode' => 'true');

        $this->request->initialize($params);

        $this->assertTrue($this->request->testMode());
    }
}
