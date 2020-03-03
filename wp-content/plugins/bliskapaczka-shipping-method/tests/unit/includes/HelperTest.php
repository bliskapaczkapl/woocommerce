<?php

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    protected function setUp()
    {
        $this->getMockBuilder(\Bliskapaczka_Map_Shipping_Method::class)
             ->disableOriginalConstructor()
             ->getMock();

        $this->getMockBuilder(\Bliskapaczka_Courier_Shipping_Method::class)
             ->disableOriginalConstructor()
             ->getMock();
    }

    public function testClassHasMethods()
    {
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getParcelDimensions'));
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getLowestPrice'));
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getPriceForCarrier'));
    }

    public function testConstants()
    {
        $hepler = new Bliskapaczka_Shipping_Method_Helper();

        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_X',
            $hepler::SIZE_TYPE_FIXED_SIZE_X
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Y',
            $hepler::SIZE_TYPE_FIXED_SIZE_Y
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Z',
            $hepler::SIZE_TYPE_FIXED_SIZE_Z
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_WEIGHT',
            $hepler::SIZE_TYPE_FIXED_SIZE_WEIGHT
        );

        $this->assertEquals(
            'BLISKAPACZKA_API_KEY',
            $hepler::API_KEY
        );
        $this->assertEquals(
            'BLISKAPACZKA_TEST_MODE',
            $hepler::TEST_MODE
        );

        $this->assertEquals(
            'BLISKAPACZKA_SENDER_EMAIL',
            $hepler::SENDER_EMAIL
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_FIRST_NAME',
            $hepler::SENDER_FIRST_NAME
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_LAST_NAME',
            $hepler::SENDER_LAST_NAME
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_PHONE_NUMBER',
            $hepler::SENDER_PHONE_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_STREET',
            $hepler::SENDER_STREET
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_BUILDING_NUMBER',
            $hepler::SENDER_BUILDING_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_FLAT_NUMBER',
            $hepler::SENDER_FLAT_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_POST_CODE',
            $hepler::SENDER_POST_CODE
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_CITY',
            $hepler::SENDER_CITY
        );
        $this->assertEquals(
            'BLISKAPACZKA_GOOGLE_MAP_API_KEY',
            $hepler::GOOGLE_MAP_API_KEY
        );
    }

    /**
     * @dataProvider parcelDimensionsModuleSettings
     */
    public function getParcelDimensions($settings, $expectedValue)
    {
        $hepler = new Bliskapaczka_Shipping_Method_Helper();
        $this->assertEquals($expectedValue, $hepler->getParcelDimensions($settings));
    }

    public function parcelDimensionsModuleSettings()
    {
        return [
            [
                [
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_X' => 10,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Y' => 10,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Z' => 10,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_WEIGHT' => 1
                ],
                ["height" => 10, "length" => 10, "width" => 10, "weight" => 1]
            ],
            [
                [
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_X' => 16,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Y' => 16,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Z' => 10,
                    'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_WEIGHT' => 10
                ],
                ["height" => 16, "length" => 16, "width" => 10, "weight" => 10]
            ]
        ];
    }

    /**
     * @dataProvider googleMapModuleSettings
     */
    public function testGetGoogleMapApiKey($apiKey, $expectedValue)
    {
        $hepler = new Bliskapaczka_Shipping_Method_Helper();
        $this->assertEquals($expectedValue, $hepler->getGoogleMapApiKey($apiKey));
    }

    public function googleMapModuleSettings()
    {
        return [
            [['BLISKAPACZKA_GOOGLE_MAP_API_KEY' => false] , 'AIzaSyCUyydNCGhxGi5GIt5z5I-X6hofzptsRjE'],
            [['BLISKAPACZKA_GOOGLE_MAP_API_KEY' => 'abcd'], 'abcd']
        ];
    }

    public function testGetLowestPrice()
    {
        $priceListEachOther = '[
            {
                "operatorName":"INPOST",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"RUCH",
                "availabilityStatus":true,
                "price":{"net":4.87,"vat":1.12,"gross":5.99},
                "unavailabilityReason":null
            },
            {
                "operatorName":"POCZTA",
                "availabilityStatus":true,
                "price":{"net":7.31,"vat":1.68,"gross":8.99},
                "unavailabilityReason":null
            }]';
        $priceListOneTheSame = '[
            {
                "operatorName":"INPOST",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"RUCH",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"POCZTA",
                "availabilityStatus":true,
                "price":{"net":7.31,"vat":1.68,"gross":8.99},
                "unavailabilityReason":null
            }]';
        $priceListOnlyOne = '[
            {
                "operatorName":"INPOST",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"RUCH",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"POCZTA",
                "availabilityStatus":false,
                "price":null,
                "unavailabilityReason": {
                    "errors": {
                        "messageCode": "ppo.api.error.pricing.algorithm.constraints.dimensionsTooSmall",
                        "message": "Allowed parcel dimensions too small. Min dimensions: 16x10x1 cm",
                        "field": null,
                        "value": null
                    }
                }
            }]';

        $hepler = new Bliskapaczka_Shipping_Method_Helper();

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListEachOther));
        $this->assertEquals(5.99, $lowestPrice);

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListOneTheSame));
        $this->assertEquals(8.99, $lowestPrice);

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListOnlyOne));
        $this->assertEquals(10.27, $lowestPrice);

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListEachOther), false);
        $this->assertEquals(4.87, $lowestPrice);

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListOneTheSame), false);
        $this->assertEquals(7.31, $lowestPrice);

        $lowestPrice = $hepler->getLowestPrice(json_decode($priceListOnlyOne), false);
        $this->assertEquals(8.35, $lowestPrice);
    }

    public function testGetPriceForCarrier()
    {
        $priceList = '[
            {
                "operatorName":"INPOST",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"RUCH",
                "availabilityStatus":true,
                "price":{"net":4.87,"vat":1.12,"gross":5.99},
                "unavailabilityReason":null
            },
            {
                "operatorName":"POCZTA",
                "availabilityStatus":true,
                "price":{"net":7.31,"vat":1.68,"gross":8.99},
                "unavailabilityReason":null
            }]';
        $hepler = new Bliskapaczka_Shipping_Method_Helper();

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'INPOST');
        $this->assertEquals(10.27, $price);

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'RUCH');
        $this->assertEquals(5.99, $price);

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'POCZTA');
        $this->assertEquals(8.99, $price);

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'INPOST', false);
        $this->assertEquals(8.35, $price);

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'RUCH', false);
        $this->assertEquals(4.87, $price);

        $price = $hepler->getPriceForCarrier(json_decode($priceList), 'POCZTA', false);
        $this->assertEquals(7.31, $price);
    }

    public function testGetOperatorsForWidget()
    {
        $priceList = '[
            {
                "operatorName":"INPOST",
                "availabilityStatus":true,
                "price":{"net":8.35,"vat":1.92,"gross":10.27},
                "unavailabilityReason":null
            },
            {
                "operatorName":"RUCH",
                "availabilityStatus":true,
                "price":{"net":4.87,"vat":1.12,"gross":5.99},
                "unavailabilityReason":null
            },
            {
                "operatorName":"POCZTA",
                "availabilityStatus":false,
                "price":null,
                "unavailabilityReason": {
                    "errors": {
                        "messageCode": "ppo.api.error.pricing.algorithm.constraints.dimensionsTooSmall",
                        "message": "Allowed parcel dimensions too small. Min dimensions: 16x10x1 cm",
                        "field": null,
                        "value": null
                    }
                }
            }]';
           $cods = array(
               'POCZTA' => 5,
               'INPOST' => 0,
               'RUCH' => 1
           );
        $helper = new Bliskapaczka_Shipping_Method_Helper();

        $this->assertEquals(
            '[{{"operator":"INPOST","price":{"net":8.35,"vat":1.92,"gross":10.27}},{"operator":"RUCH","price":{"net":4.87,"vat":1.12,"gross":5.99}}]',
            $helper->getOperatorsForWidget(0.0, json_decode($priceList), $cods)
        );
    }

    /**
     * @dataProvider phpneNumbers
     */
    public function testCleaningPhoneNumber($phoneNumber)
    {
        $hepler = new Bliskapaczka_Shipping_Method_Helper();
     
        $this->assertEquals('606606606', $hepler->telephoneNumberCeaning($phoneNumber));
    }

    public function phpneNumbers()
    {
        return [
            ['606-606-606'],
            ['606 606 606'],
            ['+48 606 606 606'],
            ['+48606606606'],
            ['+48 606-606-606'],
            ['+48-606-606-606']
        ];
    }

    public function testGetApiMode()
    {
        $hepler = new Bliskapaczka_Shipping_Method_Helper();

        $mode = $hepler->getApiMode('yes');
        $this->assertEquals('test', $mode);

        $mode = $hepler->getApiMode();
        $this->assertEquals('prod', $mode);
    }
}
