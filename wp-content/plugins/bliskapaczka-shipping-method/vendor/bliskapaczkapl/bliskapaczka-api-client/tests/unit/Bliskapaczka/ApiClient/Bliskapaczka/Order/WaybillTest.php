<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\Bliskapaczka\Order\Waybill;
use PHPUnit\Framework\TestCase;

class WaybillTest extends TestCase
{
    protected function setUp()
    {
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Order\Waybill'));
    }

    public function testGetUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Waybill($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $this->assertEquals('order/' . $id . '/waybill', $apiClientOrder->getUrl());
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid order ID
     */
    public function testGetUrlForEmptyId()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '';
        
        $apiClientOrder = new Waybill($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $apiClientOrder->getUrl();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid order ID
     */
    public function testGetUrlWithoutOrderId()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Waybill($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);

        $apiClientOrder->getUrl();
    }

    public function testGet()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Waybill($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $apiClientOrder->get();
    }
}
