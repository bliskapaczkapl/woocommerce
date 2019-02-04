<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Bliskapaczka\Todoor;
use PHPUnit\Framework\TestCase;

class TodoorValidationTest extends TestCase
{
    protected function setUp()
    {
        $this->todoorData = [
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
            "receiverStreet" => "Testowa",
            "receiverBuildingNumber" => "1",
            "receiverFlatNumber" => '11',
            "receiverPostCode" => "12-345",
            "receiverCity" => "Testowe",
            "operatorName" => "DPD",
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

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid phone number
     */
    public function testReceiverPhoneNumberValidation()
    {
        $this->todoorData['receiverPhoneNumber'] = 'string';

        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientTodoor = new Todoor($apiKey);
        $apiClientTodoor->setApiUrl($apiUrl);

        $apiClientTodoor->create($this->todoorData);
    }
}
