<?php

namespace Bliskapaczka\ApiClient\Mappers\Order;

/**
 * Order Mapper class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Validator
{
    const PHONE_NUMBER_PATTERN = '/^(5[0137]|6[069]|7[2389]|88)\d{7}$/';

    /**
     * Validate email address
     *
     * @param string $data
     */
    public static function email($data)
    {
        if (filter_var($data, FILTER_VALIDATE_EMAIL) == false) {
            throw new \Bliskapaczka\ApiClient\Exception('Invalid email', 1);
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
        preg_match(self::PHONE_NUMBER_PATTERN, $data, $phoneNumberMatches);

        if (!is_array($phoneNumberMatches) || count($phoneNumberMatches) == 0) {
            throw new \Bliskapaczka\ApiClient\Exception('Invalid phone number', 1);
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
        preg_match('/^\d{2}\-\d{3}$/', $data, $matches);

        if (!is_array($matches) || count($matches) == 0) {
            throw new \Bliskapaczka\ApiClient\Exception('Invalid post code', 1);
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
            throw new \Bliskapaczka\ApiClient\Exception('Invalid parcel', 1);
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
