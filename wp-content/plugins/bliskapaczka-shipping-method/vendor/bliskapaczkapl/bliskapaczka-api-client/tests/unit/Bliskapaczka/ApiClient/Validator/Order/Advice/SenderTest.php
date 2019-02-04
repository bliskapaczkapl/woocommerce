<?php

namespace Bliskapaczka\ApiClient\Validator\Order\Advice;

use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Validator\Order\Advice\Sender;
use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
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
            "senderCity" => "string"
        ];
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Validator\Order\Advice\Sender'));
    }

    public function testClassImplementInterface()
    {
        $order = new Sender();
        $this->assertTrue(is_a($order, 'Bliskapaczka\ApiClient\ValidatorInterface'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testSenderPostCodeShouldntbeValid()
    {
        $this->orderData['senderPostCode'] = 'string';

        $order = new Sender();
        $order->setData($this->orderData);
        $order->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /\w+ is required/
     */
    public function testSenderPostCodeShouldntbeNotEmpty()
    {
        unset($this->orderData['senderPostCode']);

        $order = new Sender();
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

        $order = new Sender();
        $order->setData($this->orderData);
        $order->validate();
    }

    public function testCodPayoutBankAccountNumberShouldntBeValidated()
    {
        $this->orderData['codPayoutBankAccountNumber'] = '';

        $order = new Sender();
        $order->setData($this->orderData);
        $this->assertTrue($order->validate());
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid CoD Payout Bank Account Number
     */
    public function testCodPayoutBankAccountNumber()
    {
        $this->orderData['codPayoutBankAccountNumber'] = '16102019120000910201486274';

        $order = new Sender();
        $order->setData($this->orderData);
        $order->validate();
    }
}
