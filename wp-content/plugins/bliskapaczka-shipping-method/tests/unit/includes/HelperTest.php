<?php

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
	/**
	 * @var Bliskapaczka_Shipping_Method_Helper
	 */
	private $helper;
	
    protected function setUp()
    {
        $map = $this->getMockBuilder(\Bliskapaczka_Map_Shipping_Method::class)
             ->disableOriginalConstructor()
             ->getMock();

        $courier = $this->getMockBuilder(\Bliskapaczka_Courier_Shipping_Method::class)
             ->disableOriginalConstructor()
             ->getMock();
        
        $this->helper = new Bliskapaczka_Shipping_Method_Helper();

        $ref = new ReflectionObject($this->helper);
        
        $property = $ref->getProperty('map_shipping_method');
        $property->setAccessible(true);
        $property->setValue($this->helper, $map);
        
        $property = $ref->getProperty('courier_shipping_method');
        $property->setAccessible(true);
        $property->setValue($this->helper, $courier);
    }

    public function testClassHasMethods()
    {
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getParcelDimensions'));
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getLowestPrice'));
        $this->assertTrue(method_exists('Bliskapaczka_Shipping_Method_Helper', 'getPriceForCarrier'));
    }

    public function testConstants()
    {
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_X',
            $this->helper::SIZE_TYPE_FIXED_SIZE_X
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Y',
            $this->helper::SIZE_TYPE_FIXED_SIZE_Y
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Z',
            $this->helper::SIZE_TYPE_FIXED_SIZE_Z
        );
        $this->assertEquals(
            'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_WEIGHT',
            $this->helper::SIZE_TYPE_FIXED_SIZE_WEIGHT
        );

        $this->assertEquals(
            'BLISKAPACZKA_API_KEY',
            $this->helper::API_KEY
        );
        $this->assertEquals(
            'BLISKAPACZKA_TEST_MODE',
            $this->helper::TEST_MODE
        );

        $this->assertEquals(
            'BLISKAPACZKA_SENDER_EMAIL',
            $this->helper::SENDER_EMAIL
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_FIRST_NAME',
            $this->helper::SENDER_FIRST_NAME
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_LAST_NAME',
            $this->helper::SENDER_LAST_NAME
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_PHONE_NUMBER',
            $this->helper::SENDER_PHONE_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_STREET',
            $this->helper::SENDER_STREET
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_BUILDING_NUMBER',
            $this->helper::SENDER_BUILDING_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_FLAT_NUMBER',
            $this->helper::SENDER_FLAT_NUMBER
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_POST_CODE',
            $this->helper::SENDER_POST_CODE
        );
        $this->assertEquals(
            'BLISKAPACZKA_SENDER_CITY',
            $this->helper::SENDER_CITY
        );
        $this->assertEquals(
            'BLISKAPACZKA_GOOGLE_MAP_API_KEY',
            $this->helper::GOOGLE_MAP_API_KEY
        );
    }

    /**
     * @dataProvider parcelDimensionsModuleSettings
     */
    public function getParcelDimensions($settings, $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->helper->getParcelDimensions($settings));
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
        $this->assertEquals($expectedValue, $this->helper->getGoogleMapApiKey($apiKey));
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

        $this->helper = new Bliskapaczka_Shipping_Method_Helper();

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListEachOther));
        $this->assertEquals(5.99, $lowestPrice);

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListOneTheSame));
        $this->assertEquals(8.99, $lowestPrice);

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListOnlyOne));
        $this->assertEquals(10.27, $lowestPrice);

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListEachOther), false);
        $this->assertEquals(4.87, $lowestPrice);

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListOneTheSame), false);
        $this->assertEquals(7.31, $lowestPrice);

        $lowestPrice = $this->helper->getLowestPrice(json_decode($priceListOnlyOne), false);
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

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'INPOST');
        $this->assertEquals(10.27, $price);

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'RUCH');
        $this->assertEquals(5.99, $price);

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'POCZTA');
        $this->assertEquals(8.99, $price);

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'INPOST', false);
        $this->assertEquals(8.35, $price);

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'RUCH', false);
        $this->assertEquals(4.87, $price);

        $price = $this->helper->getPriceForCarrier(json_decode($priceList), 'POCZTA', false);
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

        $this->assertEquals(
            '[{"operator":"INPOST","price":{"net":8.35,"vat":1.92,"gross":10.27}},{"operator":"RUCH","price":{"net":4.87,"vat":1.12,"gross":5.99}}]',
            $this->helper->getOperatorsForWidget(0.0, json_decode($priceList), $cods)
        );
    }

    /**
     * @dataProvider phpneNumbers
     */
    public function testCleaningPhoneNumber($phoneNumber)
    {
        $this->assertEquals('606606606', $this->helper->telephoneNumberCeaning($phoneNumber));
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
        $this->helper->isSandbox() 
        	? $this->assertEquals('test', $this->helper->getApiMode())
        	: $this->assertEquals('prod', $this->helper->getApiMode());
    }
}
