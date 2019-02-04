<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\Bliskapaczka\Order\Confirm;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    protected function setUp()
    {
        $this->operator = 'POCZTA';
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Order\Confirm'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid operator name
     */
    public function testGetUrlForEmptyId()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '';
        
        $apiClientOrder = new Confirm($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);

        $apiClientOrder->getUrl();
    }

    public function testGetUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Confirm($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOperator($this->operator);

        $this->assertEquals('orders/confirm?operatorName=' . $this->operator, $apiClientOrder->getUrl());
    }

    public function testConfirm()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Confirm($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOperator($this->operator);

        $apiClientOrder->confirm();
    }
}
