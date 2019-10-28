<?php

namespace Bliskapaczka;

use PHPUnit\Framework\TestCase;

class BliskapaczkaTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\AbstractBliskapaczka'));
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Order'));
    }

    public function testGetApiUrlForMode()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey);

        $url = $apiClient->getApiUrlForMode('prod');
        $this->assertEquals('https://api.bliskapaczka.pl', $url);

        $url = $apiClient->getApiUrlForMode('test');
        $this->assertEquals('https://api.sandbox-bliskapaczka.pl', $url);
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid api key
     */
    public function testEmptyApiKey()
    {
        $apiKey = '';
        $apiUrl = 'http://localhost:1234';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey);
    }

    public function testSetApiUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey);
        $apiClient->setApiUrl($apiUrl);

        $this->assertEquals($apiUrl, $apiClient->getApiUrl());
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Validator not exists
     */
    public function testNotExistingValidator()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey);
        $apiClient->setApiUrl($apiUrl);

        $apiClient->getValidator();
    }

    public function testMode()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey);

        $this->assertEquals('prod', $apiClient->getMode());

        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey, 'test');

        $this->assertEquals('test', $apiClient->getMode());
    }

    public function testTimeoutForProd()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey);

        $this->assertEquals(2, $apiClient->getApiTimeout());
    }

    public function testTimeoutForSandbox()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey, 'test');

        $this->assertEquals(10, $apiClient->getApiTimeout());
    }
}
