<?php

namespace Bliskapaczka\ApiClient\Mappers;

/**
 * Order Mapper class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Order
{
    private $allowedProperties = [
        'senderEmail',
        'receiverEmail',
        'senderPhoneNumber',
        'receiverPhoneNumber',
        'senderPostCode',
        'senderFirstName',
        'senderLastName',
        'senderStreet',
        'senderBuildingNumber',
        'senderFlatNumber',
        'senderCit',
        'receiverFirstName',
        'receiverLastName',
        'operatorName',
        'destinationCode',
        'postingCode',
        'codValue',
        'insuranceValue',
        'additionalInformation',
        'parcel'
    ];

    /**
     * Magic method implementation
     *
     * @param string $property
     */
    public function __get($property)
    {
        if (in_array($property, $this->allowedProperties)) {
            return $this->$property;
        }
    }

    /**
     * Magic method implementation
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        if (in_array($property, $this->allowedProperties)) {
              $this->$property = $value;
        }

        return $this;
    }

    /**
     * Create new instance of this class with data mapped form array
     *
     * @param array $data
     */
    public static function createFromArray(array $data)
    {
        $order = new self();

        foreach ($data as $key => $value) {
            $order->$key = $value;
        }

        return $order;
    }

    /**
     * Validate data
     */
    public function validate()
    {
        /* Original Bliskapaczka validator regexps
        numer konta: /^\d{26}$/
        nip: /^\d{10}$/
        kod pocztowy: /^\d{2}\-\d{3}$/
        */

        # Email validation
        if ($this->senderEmail) {
            Order\Validator::email($this->senderEmail);
        }
        Order\Validator::email($this->receiverEmail);

        # Phone number validation
        if ($this->senderPhoneNumber) {
            Order\Validator::phone($this->senderPhoneNumber);
        }
        Order\Validator::phone($this->receiverPhoneNumber);


        # Post code validation
        if ($this->senderPostCode) {
            Order\Validator::postCode($this->senderPostCode);
        }

        # Parcel validation
        Order\Validator::parcel($this->parcel);

        # Rest of string properties
        $properties = [
            'receiverFirstName',
            'receiverLastName',
            'operatorName',
            'destinationCode'
        ];

        foreach ($properties as $property) {
            if (is_null($this->$property) || strlen($this->$property) == 0) {
                throw new \Bliskapaczka\ApiClient\Exception('Invalid ' . $property, 1);
            }
        }
    }
}
