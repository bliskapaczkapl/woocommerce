<?php

namespace Bliskapaczka\ApiClient\Validator\Order;

use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Validator\Order\Receiver;
use PHPUnit\Framework\TestCase;

class ReceiverTest extends TestCase
{
    protected function setUp()
    {
        $this->orderData = [
            "receiverFirstName" => "string",
            "receiverLastName" => "string",
            "receiverPhoneNumber" => "606111111",
            "receiverEmail" => "bob@example.com",
        ];
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Validator\Order\Receiver'));
    }

    public function testClassImplementInterface()
    {
        $receiver = new Receiver();
        $this->assertTrue(is_a($receiver, 'Bliskapaczka\ApiClient\ValidatorInterface'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverPhoneNumberShouldntBeValid()
    {
        $this->orderData['receiverPhoneNumber'] = 'string';

        $receiver = new Receiver();
        $receiver->setData($this->orderData);
        $receiver->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverPhoneNumberShouldentBeValidIfEmpty()
    {
        $this->orderData['receiverPhoneNumber'] = '';

        $receiver = new Receiver();
        $receiver->setData($this->orderData);
        $receiver->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverEmailShouldntBeValid()
    {
        $this->orderData['receiverEmail'] = 'string';

        $receiver = new Receiver();
        $receiver->setData($this->orderData);
        $receiver->validate();
    }

    public function testReceiverValidationForValidData()
    {
        $receiver = new Receiver();
        $receiver->setData($this->orderData);
        $this->assertTrue($receiver->validate());
    }
}
