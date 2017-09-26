<?php

use PHPUnit\Framework\TestCase;

class CoreTest extends TestCase
{
    /**
     * @dataProvider classesNames
     */
    public function testPrepareClassFileName($class, $expected)
    {
        $this->assertEquals($expected, Bliskapaczka_Shipping_Method_Core::prepareClassFileName($class));
    }

    public function classesNames()
    {
        return [
            ['Bliskapaczka_Shipping_Method_Core', 'class-bliskapaczka-shipping-method-core.php'],
            ['Bliskapaczka_Shipping_Method_Helper', 'class-bliskapaczka-shipping-method-helper.php'],
            ['Bliskapaczka_Shipping_Method_Mapper', 'class-bliskapaczka-shipping-method-mapper.php'],
        ];
    }
}