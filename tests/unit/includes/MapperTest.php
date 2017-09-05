<?php

use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{

    protected function setUp()
    {
        $this->receiverFirstName = 'Zenek';
        $this->receiverLastName = 'Bliskopaczki';
        $this->receiverPhoneNumber = '504 445 665';
        $this->receiverEmail = 'zenek.bliskopaczki@sendit.pl';
        $this->operatorName = 'INPOST';
        $this->destinationCode = 'KRA010';

        // $this->addressMock = $this->getMockBuilder(\Address::class)
        //                             ->disableOriginalConstructor()
        //                             ->disableOriginalClone()
        //                             ->disableArgumentCloning()
        //                             ->disallowMockingUnknownTypes()
        //                             ->setMethods(array())
        //                             ->getMock();

        // $this->addressMock->firstname = $this->receiverFirstName;
        // $this->addressMock->lastname = $this->receiverLastName;
        // $this->addressMock->phone_mobile = $this->receiverPhoneNumber;


        $this->orderMock = $this->getMockBuilder(WC_Order::class)
                                     ->disableOriginalConstructor()
                                     ->disableOriginalClone()
                                     ->disableArgumentCloning()
                                     ->disallowMockingUnknownTypes()
                                     ->setMethods(array())
                                     ->getMock();

        $this->orderMock->pos_operator = $this->operatorName;
        $this->orderMock->pos_code = $this->destinationCode;

        // $this->customerMock = $this->getMockBuilder(\Customer::class)
        //                              ->disableOriginalConstructor()
        //                              ->disableOriginalClone()
        //                              ->disableArgumentCloning()
        //                              ->disallowMockingUnknownTypes()
        //                              ->setMethods(array())
        //                              ->getMock();

        $this->customerMock->email = $this->receiverEmail;

        $this->helperMock = $this->getMockBuilder(Bliskapaczka_Shipping_Method_Helper::class)
                                     ->disableOriginalConstructor()
                                     ->disableOriginalClone()
                                     ->disableArgumentCloning()
                                     ->disallowMockingUnknownTypes()
                                     ->setMethods(
                                         array(
                                             'getParcelDimensions',
                                             'telephoneNumberCeaning'
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

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Bliskapaczka_Shipping_Method_Mapper'));
    }

    public function testTypeOfReturnedData()
    {
        $mapper = new Bliskapaczka_Shipping_Method_Mapper();
        $data = $mapper->getData($this->orderMock, $this->helperMock);

        $this->assertTrue(is_array($data));
    }

    public function testMapperForReceiverFirstName()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals($this->receiverFirstName, $data['receiverFirstName']);
    }

    public function testMapperForReceiverLastName()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals($this->receiverLastName, $data['receiverLastName']);
    }

    public function testMapperForReceiverPhoneNumber()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals('504445665', $data['receiverPhoneNumber']);
    }

    public function testMapperForReceiverEmail()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals($this->receiverEmail, $data['receiverEmail']);
    }

    public function testMapperForOperatorName()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals($this->operatorName, $data['operatorName']);
    }

    public function testMapperForDestinationCode()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertEquals($this->destinationCode, $data['destinationCode']);
    }

    public function testMapperForParcel()
    {
        $mapper = new \Bliskapaczka\Prestashop\Core\Mapper\Order();
        $data = $mapper->getData($this->orderMock, $this->addressMock, $this->customerMock, $this->helperMock);

        $this->assertTrue(is_array($data['parcel']));
    }
}
