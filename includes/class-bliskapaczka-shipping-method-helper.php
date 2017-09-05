<?php

/**
 * Bliskapaczka Core class
 */
class Bliskapaczka_Shipping_Method_Helper
{
	const DEFAULT_GOOGLE_API_KEY = 'AIzaSyCUyydNCGhxGi5GIt5z5I-X6hofzptsRjE';

    const PARCEL_SIZE_TYPE_XML_PATH = 'carriers/sendit_bliskapaczka/parcel_size_type';
    const PARCEL_TYPE_FIXED_SIZE_X_XML_PATH = 'carriers/sendit_bliskapaczka/parcel_size_type_fixed_size_x';
    const PARCEL_TYPE_FIXED_SIZE_Y_XML_PATH = 'carriers/sendit_bliskapaczka/parcel_size_type_fixed_size_y';
    const PARCEL_TYPE_FIXED_SIZE_Z_XML_PATH = 'carriers/sendit_bliskapaczka/parcel_size_type_fixed_size_z';
    const PARCEL_TYPE_FIXED_SIZE_WEIGHT_XML_PATH = 'carriers/sendit_bliskapaczka/parcel_size_type_fixed_size_weight';
    
    const SENDER_EMAIL = 'carriers/sendit_bliskapaczka/sender_email';
    const SENDER_FIRST_NAME = 'carriers/sendit_bliskapaczka/sender_first_name';
    const SENDER_LAST_NAME = 'carriers/sendit_bliskapaczka/sender_last_name';
    const SENDER_PHONE_NUMBER = 'carriers/sendit_bliskapaczka/sender_phone_number';
    const SENDER_STREET = 'carriers/sendit_bliskapaczka/sender_street';
    const SENDER_BUILDING_NUMBER = 'carriers/sendit_bliskapaczka/sender_building_number';
    const SENDER_FLAT_NUMBER = 'carriers/sendit_bliskapaczka/sender_flat_number';
    const SENDER_POST_CODE = 'carriers/sendit_bliskapaczka/sender_post_code';
    const SENDER_CITY = 'carriers/sendit_bliskapaczka/sender_city';

    const API_KEY_XML_PATH = 'carriers/sendit_bliskapaczka/bliskapaczkaapikey';
    const API_TEST_MODE_XML_PATH = 'carriers/sendit_bliskapaczka/test_mode';

    const GOOGLE_MAP_API_KEY_XML_PATH = 'carriers/sendit_bliskapaczka/google_map_api_key';

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
     * @return string
     */
    public function getGoogleMapApiKey()
    {
        $googleApiKey = self::DEFAULT_GOOGLE_API_KEY;

        // if (\Configuration::get(self::GOOGLE_MAP_API_KEY)) {
        //     $googleApiKey = \Configuration::get(self::GOOGLE_MAP_API_KEY);
        // }

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
        $apiClient = $this->getApiClient();
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
     * @return \Bliskapaczka\ApiClient\Bliskapaczka
     */
    public function getApiClient()
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka(
        	'0854c14f-ab66-42a8-8710-e737bf062fb2', 'test'
            // Mage::getStoreConfig(self::API_KEY_XML_PATH),
            // $this->getApiMode(Mage::getStoreConfig(self::API_TEST_MODE_XML_PATH))
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
            case '1':
                $mode = 'test';
                break;

            default:
                $mode = 'prod';
                break;
        }

        return $mode;
    }
}
