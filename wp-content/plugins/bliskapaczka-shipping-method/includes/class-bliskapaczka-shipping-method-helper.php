<?php

/**
 * Bliskapaczka Core class
 */
class Bliskapaczka_Shipping_Method_Helper
{
    const DEFAULT_GOOGLE_API_KEY =  'AIzaSyCUyydNCGhxGi5GIt5z5I-X6hofzptsRjE';

    const SIZE_TYPE_FIXED_SIZE_X = 'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_X';
    const SIZE_TYPE_FIXED_SIZE_Y = 'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Y';
    const SIZE_TYPE_FIXED_SIZE_Z = 'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_Z';
    const SIZE_TYPE_FIXED_SIZE_WEIGHT = 'BLISKAPACZKA_PARCEL_SIZE_TYPE_FIXED_SIZE_WEIGHT';

    const SENDER_EMAIL = 'BLISKAPACZKA_SENDER_EMAIL';
    const SENDER_FIRST_NAME = 'BLISKAPACZKA_SENDER_FIRST_NAME';
    const SENDER_LAST_NAME = 'BLISKAPACZKA_SENDER_LAST_NAME';
    const SENDER_PHONE_NUMBER = 'BLISKAPACZKA_SENDER_PHONE_NUMBER';
    const SENDER_STREET = 'BLISKAPACZKA_SENDER_STREET';
    const SENDER_BUILDING_NUMBER = 'BLISKAPACZKA_SENDER_BUILDING_NUMBER';
    const SENDER_FLAT_NUMBER = 'BLISKAPACZKA_SENDER_FLAT_NUMBER';
    const SENDER_POST_CODE = 'BLISKAPACZKA_SENDER_POST_CODE';
    const SENDER_CITY = 'BLISKAPACZKA_SENDER_CITY';

    const API_KEY = 'BLISKAPACZKA_API_KEY';
    const TEST_MODE = 'BLISKAPACZKA_TEST_MODE';

    const GOOGLE_MAP_API_KEY = 'BLISKAPACZKA_GOOGLE_MAP_API_KEY';

    /**
     * Get parcel dimensions in format accptable by Bliskapaczka API
     *
     * @return array
     */
    public function getParcelDimensions()
    {
        // $type = Mage::getStoreConfig(self::PARCEL_SIZE_TYPE_XML_PATH);
        $height = 16; # Mage::getStoreConfig(self::PARCEL_TYPE_FIXED_SIZE_X_XML_PATH);
        $length = 16; #  Mage::getStoreConfig(self::PARCEL_TYPE_FIXED_SIZE_Y_XML_PATH);
        $width = 16; #  Mage::getStoreConfig(self::PARCEL_TYPE_FIXED_SIZE_Z_XML_PATH);
        $weight = 1; #  Mage::getStoreConfig(self::PARCEL_TYPE_FIXED_SIZE_WEIGHT_XML_PATH);

        $dimensions = array(
            "height" => $height,
            "length" => $length,
            "width" => $width,
            "weight" => $weight
        );

        return $dimensions;
    }

/**
     * Get Google API key. If key is not defined return default.
     *
     * @param array $settings
     * @return string
     */
    public function getGoogleMapApiKey($settings)
    {
        $googleApiKey = self::DEFAULT_GOOGLE_API_KEY;

        if ($settings[self::GOOGLE_MAP_API_KEY]) {
            $googleApiKey = $settings[self::GOOGLE_MAP_API_KEY];
        }

        return $googleApiKey;
    }

    /**
     * Get lowest price from pricing list
     *
     * @param array $priceList - price list
     * @param bool $taxInc - return price with tax
     * @return float
     */
    public function getLowestPrice($priceList, $taxInc = true)
    {
        $lowestPriceTaxExc = null;
        $lowestPriceTaxInc = null;

        foreach ($priceList as $carrier) {
            if ($carrier->availabilityStatus == false) {
                continue;
            }

            if ($lowestPriceTaxInc == null || $lowestPriceTaxInc > $carrier->price->gross) {
                $lowestPriceTaxExc = $carrier->price->net;
                $lowestPriceTaxInc = $carrier->price->gross;
            }
        }

        if ($taxInc) {
            $lowestPrice = $lowestPriceTaxInc;
        } else {
            $lowestPrice = $lowestPriceTaxExc;
        }

        return $lowestPrice;
    }

    /**
     * Get price for specific carrier
     *
     * @param array $priceList
     * @param string $carrierName
     * @param bool $taxInc
     * @return float
     */
    public function getPriceForCarrier($priceList, $carrierName, $taxInc = true)
    {
        $price = null;

        foreach ($priceList as $carrier) {
            if ($carrier->operatorName == $carrierName) {
                if ($taxInc) {
                    $price = $carrier->price->gross;
                } else {
                    $price = $carrier->price->net;
                }
            }
        }

        return $price;
    }

    /**
     * Get operators and prices from Bliskapaczka API
     *
     * @return string
     */
    public function getPriceList()
    {
        /* @var Bliskapaczka_Shipping_Method $bliskapaczka */
        $bliskapaczka = new Bliskapaczka_Shipping_Method();

        $apiClient = $this->getApiClient(
            $bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $bliskapaczka->settings['BLISKAPACZKA_TEST_MODE']
        );
        $priceList = $apiClient->getPricing(
            array("parcel" => array('dimensions' => $this->getParcelDimensions()))
        );

        return json_decode($priceList);
    }

    /**
     * Get widget configuration
     *
     * @param array $priceList
     * @return array
     */
    public function getOperatorsForWidget($priceList = null)
    {
        if (!$priceList) {
            $priceList = $this->getPriceList();
        }
        $operators = array();

        foreach ($priceList as $operator) {
            if ($operator->availabilityStatus != false) {
                $operators[] = array(
                    "operator" => $operator->operatorName,
                    "price" => $operator->price->gross
                );
            }
        }

        return json_encode($operators);
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @param string $apiKey
     * @param string $mode
     * @return \Bliskapaczka\ApiClient\Bliskapaczka
     */
    public function getApiClient($apiKey, $mode)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka(
        	$apiKey,
            $this->getApiMode($mode)
        );

        return $apiClient;
    }

    /**
     * Remove all non numeric chars from phone number
     *
     * @param string $phoneNumber
     * @return string
     */
    public function telephoneNumberCeaning($phoneNumber)
    {
        $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

        if (strlen($phoneNumber) > 9) {
            $phoneNumber = preg_replace("/^48/", "", $phoneNumber);
        }
        
        return $phoneNumber;
    }

    /**
     * Get API mode
     *
     * @param string $configValue
     * @return string
     */
    public function getApiMode($configValue = '')
    {
        $mode = '';

        switch ($configValue) {
            case 'yes':
                $mode = 'test';
                break;

            default:
                $mode = 'prod';
                break;
        }

        return $mode;
    }
}
