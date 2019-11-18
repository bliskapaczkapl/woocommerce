<?php

namespace Bliskapaczka\ApiClient;

use Psr\Log\LoggerInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
abstract class AbstractValidator
{
    const PHONE_NUMBER_PATTERN = '/^\\d{9}$/';

    protected $operator = [
        'senderEmail' => 'email',
        'receiverEmail' => 'email',
        'senderPhoneNumber' => 'phone',
        'receiverPhoneNumber' => 'phone',
        'senderPostCode' => 'postCode',
        'receiverPostCode' => 'postCode',
        'parcel' => 'parcel',
        'codPayoutBankAccountNumber' => 'iban'
    ];

    /**
     * Set data to validata
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Basic validation for data
     */
    protected function validationByProperty()
    {
        foreach ($this->properties as $property => $settings) {
            if (!isset($this->data[$property])) {
                continue;
            }

            $this->notBlank($property, $settings);
            $this->maxLength($property, $settings);
            $this->specificValidation($property);
        }
    }

    /**
     * Set of specific method for order validation
     *
     * @param string $property
     */
    protected function specificValidation($property)
    {
        if (isset($this->data[$property])
            && isset($this->operator[$property])
            && method_exists($this, $this->operator[$property])
        ) {
            call_user_func('self::' . $this->operator[$property], $this->data[$property]);
        }
    }

    /**
     * Validation for not blank properties
     *
     * @param string $property
     * @param array $settings
     */
    protected function notBlank($property, $settings)
    {
        if (isset($settings['notblank'])
            && isset($settings['notblank']) === true
            && (is_null($this->data[$property]) || strlen($this->data[$property]) == 0)
        ) {
            throw new Exception('Invalid ' . $property, 1);
        }
    }

    /**
     * Validation of lenght
     *
     * @param string $property
     * @param array $settings
     */
    protected function maxLength($property, $settings)
    {
        if (isset($settings['maxlength'])
            && $settings['maxlength'] > 0
            && strlen($this->data[$property]) > $settings['maxlength']
        ) {
            throw new Exception('Invalid ' . $property, 1);
        }
    }

    /**
     * Validate email address
     *
     * @param string $data
     */
    public static function email($data)
    {
        if (!empty($data)) {
            if (filter_var($data, FILTER_VALIDATE_EMAIL) == false) {
                throw new \Bliskapaczka\ApiClient\Exception('Invalid email', 1);
            }
        }

        return true;
    }

    /**
     * Validate phone number
     *
     * @param string $data
     */
    public static function phone($data)
    {
        if (!empty($data)) {
            preg_match(self::PHONE_NUMBER_PATTERN, $data, $phoneNumberMatches);

            if (!is_array($phoneNumberMatches) || count($phoneNumberMatches) == 0) {
                throw new \Bliskapaczka\ApiClient\Exception('Invalid phone number', 1);
            }
        }

        return true;
    }

    /**
     * Validate bank account
     *
     * @param string $data
     */
    public static function iban($data)
    {
        if (!empty($data)) {
            $iban = new \IBAN('PL' . $data);
            if (!$iban->Verify()) {
                throw new \Bliskapaczka\ApiClient\Exception('Invalid CoD Payout Bank Account Number', 1);
            }
        }

        return true;
    }

    /**
     * Validate postcode
     *
     * @param string $data
     */
    public static function postCode($data)
    {
        if (!empty($data)) {
            preg_match('/^\d{2}\-\d{3}$/', $data, $matches);

            if (!is_array($matches) || count($matches) == 0) {
                throw new \Bliskapaczka\ApiClient\Exception('Invalid post code', 1);
            }
        }

        return true;
    }

    /**
     * Validate parcel
     *
     * @param array $data
     */
    public static function parcel($data)
    {
        if (!is_array($data) || !array_key_exists('dimensions', $data)) {
            throw new Exception('Invalid parcel', 1);
        }

        $dimensions = ['height', 'length', 'width', 'weight'];

        # Parcel dimesnsions should be graten than 0
        foreach ($dimensions as $dimension) {
            if ($data['dimensions'][$dimension] <= 0) {
                throw new \Bliskapaczka\ApiClient\Exception('Dimesnion must be greater than 0', 1);
            }
        }
    }
}
