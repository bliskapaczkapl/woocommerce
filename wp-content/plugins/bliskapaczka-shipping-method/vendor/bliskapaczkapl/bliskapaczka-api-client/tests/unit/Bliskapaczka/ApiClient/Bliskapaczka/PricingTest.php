<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\Bliskapaczka\Pricing;
use PHPUnit\Framework\TestCase;

class PricingTest extends TestCase
{
    protected function setUp()
    {
        $this->pricingData = [
            "parcel" => [
                "dimensions" => [
                    "height" => 20,
                    "length" => 20,
                    "width" => 20,
                    "weight" => 2
                ]
            ],
            "deliveryType" => "P2P"
        ];
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Pricing'));
    }

    public function testGetUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientPricing = new Pricing($apiKey);
        $apiClientPricing->setApiUrl($apiUrl);

        $this->assertEquals('pricing', $apiClientPricing->getUrl());
    }

    public function testGet()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientPricing = new Pricing($apiKey);
        $apiClientPricing->setApiUrl($apiUrl);

        $apiClientPricing->get($this->pricingData);
    }
}
