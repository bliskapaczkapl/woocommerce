<?php

namespace Bliskapaczka\ApiClient;

use Bliskapaczka\ApiClient\Mappers\Order;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka\ApiClient\Mappers\Order\Validator'));
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid email
     */
    public function testForInvalidEmail()
    {
        Order\Validator::email('string');
    }

    public function testForValidEmail()
    {
        $this->assertTrue(Order\Validator::email('bob@example.com'));
    }

    /**
     * @dataProvider invalidPhoneNumbers
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid phone number
     */
    public function testForInvalidPhoneNumber($phoneNumber)
    {
        Order\Validator::phone($phoneNumber);
    }

    public function invalidPhoneNumbers()
    {
        return [
            ['string'],
            ['604 555 555'],
            ['608-555-333']
        ];
    }

    /**
     * @dataProvider validPhoneNumbers
     */
    public function testForValidPhoneNumber($phoneNumber)
    {
        Order\Validator::phone($phoneNumber);
    }

    public function validPhoneNumbers()
    {
        return [
            ['606567765'],
            ['505333321']
        ];
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid post code
     */
    public function testForInvalidPostCode()
    {
        Order\Validator::postCode('string');
    }

    public function testForValidPostCode()
    {
        Order\Validator::postCode('54-125');
    }

    /**
     * @expectedException Bliskapaczka\ApiClient\Exception
     * @expectedExceptionMessage Invalid parcel
     */
    public function testParcel()
    {
        Order\Validator::parcel('string');
    }
}
