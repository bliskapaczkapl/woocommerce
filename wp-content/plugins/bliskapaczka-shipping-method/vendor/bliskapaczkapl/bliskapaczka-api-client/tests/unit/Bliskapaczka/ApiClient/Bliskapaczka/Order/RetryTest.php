<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\Bliskapaczka\Order\Retry;
use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    protected function setUp()
    {
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Order\Retry'));
    }

    public function testGetUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Retry($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $this->assertEquals('order/' . $id . '/retry', $apiClientOrder->getUrl());
    }

    public function testRetry()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Retry($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $apiClientOrder->retry();
    }
}
