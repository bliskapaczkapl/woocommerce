<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\Bliskapaczka\Pos;
use PHPUnit\Framework\TestCase;

class PosTest extends TestCase
{
    protected function setUp()
    {
        $this->operator = 'ruch';
        $this->pointCode = '112345';
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Bliskapaczka\Pos'));
    }

    public function testGetUrl()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientPos = new Pos($apiKey);
        $apiClientPos->setApiUrl($apiUrl);
        $apiClientPos->setOperator($this->operator);
        $apiClientPos->setPointCode($this->pointCode);

        $this->assertEquals('pos/' . $this->operator . '/' . $this->pointCode, $apiClientPos->getUrl());
    }

    public function testGet()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';
        
        $apiClientPos = new Pos($apiKey);
        $apiClientPos->setApiUrl($apiUrl);
        $apiClientPos->setOperator($this->operator);
        $apiClientPos->setPointCode($this->pointCode);

        $apiClientPos->get();
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid operator name or valid point code
     */
    public function testGetUrlForEmptyPointCode()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';

        $apiClientPos = new Pos($apiKey);
        $apiClientPos->setOperator($this->operator);

        var_dump($apiClientPos->getUrl());
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Please set valid operator name or valid point code
     */
    public function testGetUrlForEmptyOperator()
    {
        $apiKey = '6061914b-47d3-42de-96bf-0004a57f1dba';
        $apiUrl = 'http://localhost:1234';

        $apiClientPos = new Pos($apiKey);
        $apiClientPos->setPointCode($this->pointCode);

        var_dump($apiClientPos->getUrl());
    }
}
