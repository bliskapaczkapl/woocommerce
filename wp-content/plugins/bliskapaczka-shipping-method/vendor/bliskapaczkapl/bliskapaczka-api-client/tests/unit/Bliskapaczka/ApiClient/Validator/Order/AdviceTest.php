<?php

namespace Bliskapaczka\ApiClient\Validator\Order;

use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Validator\Order\Advice;
use PHPUnit\Framework\TestCase;

class AdviceTest extends TestCase
{
    protected function setUp()
    {
        $this->orderData = [
            "senderFirstName" => "string",
            "senderLastName" => "string",
            "senderPhoneNumber" => "111111111",
            "senderEmail" => "bob@example.com",
            "senderStreet" => "string",
            "senderBuildingNumber" => "string",
            "senderFlatNumber" => "string",
            "senderPostCode" => "54-130",
            "senderCity" => "string",
            "receiverFirstName" => "string",
            "receiverLastName" => "string",
            "receiverPhoneNumber" => "111111111",
            "receiverEmail" => "eva@example.com",
            "deliveryType" => "P2P",
            "operatorName" => "INPOST",
            "destinationCode" => "KRA010",
            "postingCode" => "KRA011",
            "codValue" => 0,
            "additionalInformation" => "string",
            "parcel" => [
                "dimensions" => [
                    "height" => 20,
                    "length" => 20,
                    "width" => 20,
                    "weight" => 2
                ],
                "insuranceValue" => 0
            ]
        ];
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Validator\Order\Advice'));
    }

    public function testClassImplementInterface()
    {
        $order = new Advice();
        $this->assertTrue(is_a($order, 'Bliskapaczka\ApiClient\ValidatorInterface'));
    }

    public function testValid()
    {
        unset($this->orderData['postingCode']);

        $order = new Advice();
        $order->setData($this->orderData);
        $order->validate();
    }

    public function testValidForSavedOrder()
    {
        $this->orderData['number'] = '000000001P-000000002';

        $order = new Advice();
        $order->setData($this->orderData);
        $order->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testSenderPostCodeShouldntbeValid()
    {
        $this->orderData['senderPostCode'] = 'string';

        $order = new Advice();
        $order->setData($this->orderData);
        $order->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /\w+ is required/
     */
    public function testSenderPhoneNumberShouldBeSetted()
    {
        unset($this->orderData['senderPhoneNumber']);

        $order = new Advice();
        $order->setData($this->orderData);
        $order->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testSenderPhoneNumberShouldntBeString()
    {
        $this->orderData['senderPhoneNumber'] = 'string';

        $order = new Advice();
        $order->setData($this->orderData);
        $order->validate();
    }
}
