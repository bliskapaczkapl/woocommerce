<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\Bliskapaczka\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    protected function setUp()
    {
        $this->orderData = [
            "senderFirstName" => "string",
            "senderLastName" => "string",
            "senderPhoneNumber" => "606555433",
            "senderEmail" => "bob@example.com",
            "senderStreet" => "string",
            "senderBuildingNumber" => "string",
            "senderFlatNumber" => "string",
            "senderPostCode" => "54-130",
            "senderCity" => "string",
            "receiverFirstName" => "string",
            "receiverLastName" => "string",
            "receiverPhoneNumber" => "600555432",
            "receiverEmail" => "eva@example.com",
            "deliveryType" => "P2P",
            "operatorName" => "INPOST",
            "destinationCode" => "KRA010",
            "postingCode" => "KRA011",
            "codValue" => 0,
            "insuranceValue" => 0,
            "additionalInformation" => "string",
            "parcel" => [
                "dimensions" => [
                    "height" => 20,
                    "length" => 20,
                    "width" => 20,
                    "weight" => 2
                ]
            ]
        ];
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Order'));
    }

    public function testGetUrlForCreateMethod()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);

        $this->assertEquals('order', $apiClientOrder->getUrl());
    }

    public function testGetUrlForGetMethod()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $this->assertEquals('order/' . $id, $apiClientOrder->getUrl());
    }

    public function testCreate()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);

        $apiClientOrder->create($this->orderData);
    }

    public function testGetValidator()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);

        $this->assertTrue(is_a($apiClientOrder->getValidator(), 'Bliskapaczka\ApiClient\Validator\Order'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid order ID
     */
    public function testGetForEmptyId()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $apiClientOrder->get($this->orderData);
    }

    public function testGet()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $id = '000000001P-000000002';
        
        $apiClientOrder = new Order($apiKey);
        $apiClientOrder->setApiUrl($apiUrl);
        $apiClientOrder->setOrderId($id);

        $apiClientOrder->get();
    }
}
