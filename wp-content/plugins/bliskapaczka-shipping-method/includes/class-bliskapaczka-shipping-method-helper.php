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
    const TITLE = 'BLISKAPACZKA_TITLE';

    const GOOGLE_MAP_API_KEY = 'BLISKAPACZKA_GOOGLE_MAP_API_KEY';

    const COD_ONLY = 'BLISKAPACZKA_COD_ONLY';

    const BANK_ACCOUNT_NUMBER = 'BLISKAPACZKA_BANK_ACCOUNT_NUMBER';

    const TITLE_COURIER = 'BLISKAPACZKA_COURIER_TITLE';

    const ENABLE_COURIER = 'BLISKAPACZKA_COURIER_ENABLE';


    /**
     * Get parcel dimensions in format accptable by Bliskapaczka API
     *
     * @return array
     */
    public function getParcelDimensions($settings)
    {
        // $type = Mage::getStoreConfig(self::PARCEL_SIZE_TYPE_XML_PATH);
        $height = $settings[self::SIZE_TYPE_FIXED_SIZE_X];
        $length = $settings[self::SIZE_TYPE_FIXED_SIZE_Y];
        $width = $settings[self::SIZE_TYPE_FIXED_SIZE_Z];
        $weight = $settings[self::SIZE_TYPE_FIXED_SIZE_WEIGHT];

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
     * @param bool $cod
     *
     * @return float
     * @throws \Bliskapaczka\ApiClient\Exception
     */
    public function getPriceForCarrier($priceList, $carrierName, $taxInc = true, $cod = false)
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
     * @param array|null $data
     *
     * @return string
     */
    public function getPriceList(array $data = null)
    {
        /* @var Bliskapaczka_Shipping_Method $bliskapaczka */
        $bliskapaczka = new Bliskapaczka_Map_Shipping_Method();
        if (is_null($data)) {
            $data = array(
              "parcel" => array(
                  'dimensions' => $this->getParcelDimensions($bliskapaczka->settings)
              )
            );
        }
        $apiClient = $this->getApiClientPricing($bliskapaczka);
        $priceList = $apiClient->get($data);

        return json_decode($priceList);
    }

    public function getPriceListForCourier($cart_total, $priceList = null, $is_cod = false)
    {
        if (is_null($priceList)) {
            /* @var Bliskapaczka_Shipping_Method $bliskapaczka */
            $bliskapaczka = new Bliskapaczka_Map_Shipping_Method();
            $data = array(
                "parcel" => array(
                    'dimensions' => $this->getParcelDimensions($bliskapaczka->settings)
                )
            );
            if ($is_cod === true) {
                $data['codValue'] = $cart_total;
            }
            $data['deliveryType'] = 'D2D';
            $priceList = $this->getPriceList($data);

        }
        $operators = array();
        foreach ($priceList as $item) {
            if ($item->availabilityStatus === true) {
                $operators[] = array(
                    "operator" => $item->operatorName,
                    "price" => $item->price
                );
            }
        }

        return json_encode($operators);
    }

    /**
     * Get widget configuration
     *
     * @param float $cart_total
     * @param array $priceList
     *
     * @param bool $is_cod
     *
     * @return array
     */
    public function getOperatorsForWidget($cart_total, $priceList = null, $is_cod = false)
    {
        if (is_null($priceList)) {
            /* @var Bliskapaczka_Shipping_Method $bliskapaczka */
            $bliskapaczka = new Bliskapaczka_Map_Shipping_Method();
            $data = array(
                "parcel" => array(
                    'dimensions' => $this->getParcelDimensions($bliskapaczka->settings)
                )
            );
            if ($is_cod === true) {
                $data['codValue'] = $cart_total;
            }
            $priceList = $this->getPriceList($data);
            $data['deliveryType'] = 'D2P';
            $priceListForD2P = $this->getPriceList($data);
            $priceList = array_merge($priceList, $priceListForD2P);
        }
        $operators = array();
        foreach ($priceList as $item) {
            if ($item->availabilityStatus === true) {
                $operators[] = array(
                    "operator" => $item->operatorName,
                    "price" => $item->price
                );
            }
        }

        return json_encode($operators);
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @param Bliskapaczka_Shipping_Method $bliskapaczka
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pricing
     */
    public function getApiClientPricing($bliskapaczka)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing(
        	$bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $this->getApiMode($bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'])
        );

        return $apiClient;
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @param Bliskapaczka_Shipping_Method $bliskapaczka
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pos
     */
    public function getApiClientPos($bliskapaczka)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pos(
            $bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $this->getApiMode($bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'])
        );

        return $apiClient;
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @param Bliskapaczka_Shipping_Method $bliskapaczka
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order
     */
    public function getApiClientOrder($bliskapaczka)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Order(
            $bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $this->getApiMode($bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'])
        );

        return $apiClient;
    }

    /**
     * Get Bliskapaczka Api Client
     * @param Bliskapaczka_Shipping_Method $bliskapaczka
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Config
     * @throws \Bliskapaczka\ApiClient\Exception
     */
    public function getApiClientConfig($bliskapaczka)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Config(
            $bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $this->getApiMode($bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'])
        );

        return $apiClient;
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka
     */
    public function getApiClientPricingTodoor($bliskapaczka)
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing\Todoor(
            $bliskapaczka->settings['BLISKAPACZKA_API_KEY'],
            $this->getApiMode($bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'])
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
