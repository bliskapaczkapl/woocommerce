<?php

function wc_get_order_item_meta($id, $name) {
    $data = '';

    switch ($name) {
        case '_bliskapaczka_posOperator':
            $data = 'INPOST';
            break;
        
        case '_bliskapaczka_posCode':
            $data = 'KRA010';
            break;
    }

    return $data;
}

use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    protected function setUp()
    {
        $this->receiverFirstName = 'Zenek';
        $this->receiverLastName = 'Bliskopaczki';
        $this->receiverPhoneNumber = '504 445 665';
        $this->receiverEmail = 'zenek.bliskopaczki@bliskapaczka.pl';
        $this->operatorName = 'INPOST';
        $this->destinationCode = 'KRA010';

        $this->orderMock = $this->getMockBuilder(\WC_Order::class)
                                     ->disableOriginalConstructor()
                                     ->disableOriginalClone()
                                     ->disableArgumentCloning()
                                     ->disallowMockingUnknownTypes()
                                     ->setMethods(
                                         array(
                                             'get_items',
                                             'get_address',
                                             )
                                     )
                                     ->getMock();

        $this->orderMock->shipping = array(
            'first_name' => $this->receiverFirstName,
            'last_name' => $this->receiverLastName,            
        );

        $this->orderMock->billing = array(
            'email' => $this->receiverEmail,
            'phone' => $this->receiverPhoneNumber,
        );

        $this->orderMock->method('get_items')->will($this->returnValue(array('33' => array())));
        $this->orderMock
            ->expects($this->any())
            ->method('get_address')
            ->with(
                $this->logicalOr(
                    $this->equalTo('shipping'),
                    $this->equalTo('billing')
                )
            )
            ->will($this->returnCallback(array($this, 'get_address')));

        $this->helperMock = $this->getMockBuilder(\Bliskapaczka_Shipping_Method_Helper::class)
                                     ->disableOriginalConstructor()
                                     ->disableOriginalClone()
                                     ->disableArgumentCloning()
                                     ->disallowMockingUnknownTypes()
                                     ->setMethods(
                                         array(
                                             'getParcelDimensions',
                                             'telephoneNumberCeaning',
                                         )
                                     )
                                     ->getMock();

        $dimensions = array(
            "height" => 12,
            "length" => 12,
            "width" => 12,
            "weight" => 1
        );

        $this->helperMock->method('getParcelDimensions')->will($this->returnValue($dimensions));
        $this->helperMock->method('telephoneNumberCeaning')
            ->with($this->equalTo('504 445 665'))
            ->will($this->returnValue('504445665'));
    }

    public function get_address($type) {
        switch ($type) {
            case 'shipping':
                $address = $this->orderMock->shipping;
                break;
            
            case 'billing':
                $address = $this->orderMock->billing;
                break;
        }

        return $address;
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('\Bliskapaczka_Shipping_Method_Mapper'));
    }

    public function testTypeOfReturnedData()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertTrue(is_array($data));
    }

    public function testMapperForReceiverFirstName()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals($this->receiverFirstName, $data['receiverFirstName']);
    }

    public function testMapperForReceiverLastName()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals($this->receiverLastName, $data['receiverLastName']);
    }

    public function testMapperForReceiverPhoneNumber()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals('504445665', $data['receiverPhoneNumber']);
    }

    public function testMapperForReceiverEmail()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals($this->receiverEmail, $data['receiverEmail']);
    }

    public function testMapperForOperatorName()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals($this->operatorName, $data['operatorName']);
    }

    public function testMapperForDestinationCode()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertEquals($this->destinationCode, $data['destinationCode']);
    }

    public function testMapperForParcel()
    {
        $mapper = new \Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertTrue(is_array($data['parcel']));
    }
}
