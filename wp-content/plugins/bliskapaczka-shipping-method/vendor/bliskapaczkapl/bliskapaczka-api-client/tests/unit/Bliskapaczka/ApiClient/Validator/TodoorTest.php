<?php

namespace Bliskapaczka\ApiClient\Validator;

use Bliskapaczka\ApiClient\Validator\Todoor;
use PHPUnit\Framework\TestCase;

class TodoorTest extends TestCase
{
    protected function setUp()
    {
        $this->todoorData = [
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
            "receiverStreet" => "Testowa",
            "receiverBuildingNumber" => "1",
            "receiverFlatNumber" => '11',
            "receiverPostCode" => "12-345",
            "receiverCity" => "Testowe",
            "deliveryType" => "D2D",
            "operatorName" => "DPD",
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
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Validator\Todoor'));
    }

    public function testClassImplementInterface()
    {
        $order = new Todoor();
        $this->assertTrue(is_a($order, 'Bliskapaczka\ApiClient\ValidatorInterface'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid phone number
     */
    public function testReceiverPhoneNumberValidation()
    {
        $this->todoorData['receiverPhoneNumber'] = 'string';

        $order = new Todoor();
        $order->setData($this->todoorData);
        $order->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid post code
     */
    public function testSenderPostCodeValidation()
    {
        $this->todoorData['senderPostCode'] = 'string';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid parcel
     */
    public function testParcelsValidation()
    {
        $this->todoorData['parcel'] = 'string';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverFirstNameValidation()
    {
        $this->todoorData['receiverFirstName'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverLastNameValidation()
    {
        $this->todoorData['receiverLastName'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverStreetValidation()
    {
        $this->todoorData['receiverStreet'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverBuildingNumberValidation()
    {
        $this->todoorData['receiverBuildingNumber'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverPostCodeValidation()
    {
        $this->todoorData['receiverPostCode'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testReceiverCityValidation()
    {
        $this->todoorData['receiverCity'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessageRegExp /Invalid \w+/
     */
    public function testOperatorNameValidation()
    {
        $this->todoorData['operatorName'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Dimesnion must be greater than 0
     */
    public function testParcelDimensionsValidation()
    {
        $this->todoorData['parcel']['dimensions']['height'] = 0;

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();

        $this->todoorData['parcel']['dimensions']['height'] = -1;

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid deliveryType
     */
    public function testDeliveryTypeValidation()
    {
        $this->todoorData['deliveryType'] = '';

        $todoor = new Todoor();
        $todoor->setData($this->todoorData);
        $todoor->validate();
    }
}
