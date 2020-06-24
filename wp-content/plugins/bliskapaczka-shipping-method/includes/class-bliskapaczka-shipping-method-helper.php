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
    const AUTO_ADVICE = 'BLISKAPACZKA_AUTO_ADVICE';

    /**
     * Temporarily blocked this functionality, because is not yet finished.
     * @var string
     */
    const FUNCTIONALITY_AUTO_ADVICE_ENABLED = false;

    /**
     * Instance of helper
     * 
     * @var Bliskapaczka_Shipping_Method_Helper
     */
	private static $instance;

	/**
	 * Instance of Bliskapaczka_Map_Shipping_Method
	 * 
	 * It's lazy variable. Please use a getMapShippingMethod() method to access it.
	 * 
	 * @see Bliskapaczka_Shipping_Method_Helper::getMapShippingMethod()
	 * 
	 * @var Bliskapaczka_Map_Shipping_Method
	 */
	private $map_shipping_method;
	
    /**
	 * Instance of Bliskapaczka_Courier_Shipping_Method
	 * 
	 * It's lazy variable. Please use a getCourierShippingMethod() method to access it.
	 * 
	 * @see Bliskapaczka_Shipping_Method_Helper::getCourierShippingMethod()
	 * 
	 * @var Bliskapaczka_Courier_Shipping_Method
	 */
	private $courier_shipping_method;

	/**
	 * Price list response from API buffor.
	 * 
	 * @var array
	 */
	private $buf_price_list = array();

	/**
	 * Returns a signle instance of this helper
	 */
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

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
    	if ( isset( $settings[ self::GOOGLE_MAP_API_KEY ] ) && ! empty( $settings[ self::GOOGLE_MAP_API_KEY ] ) ) {
            return $settings[ self::GOOGLE_MAP_API_KEY ];
        }

        return self::DEFAULT_GOOGLE_API_KEY;
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
    	$bliskapaczka = $this->getMapShippingMethod();

        if (is_null($data)) {
            $data = array(
              "parcel" => array(
                  'dimensions' => $this->getParcelDimensions($bliskapaczka->settings)
              )
            );
        }

        // We generete hash for the request data, to remember response from api. 
        $hash = \md5( \json_encode( $data ) );
        
        // Take data, if it's not buffered.
        if ( ! isset( $this->buf_price_list[ $hash ]) ) {
        	$apiClient = $this->getApiClientPricing($bliskapaczka);
        	$priceList = $apiClient->get($data);
        	$this->buf_price_list[ $hash ] = json_decode($priceList);
        }

    	return $this->buf_price_list[ $hash ];
    }

    public function getPriceListForCourier($cart_total, $priceList = null, $is_cod = false)
    {
        if (is_null($priceList)) {
        	$bliskapaczka = $this->getMapShippingMethod();
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

            if ( !is_array( $priceList ) ) {
            	$priceList = array();
            }

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
     * @return string|null
     */
    public function getOperatorsForWidget($cart_total, $priceList = null, $is_cod = false)
    {	
    	static $operators;
    	// Hash is used to dected if we must request data from API again, in the same PHP process.
    	static $hash; 
    	$newHash = "" . $cart_total . $is_cod ? '_0' : '_1';
    	
    	if ( ! isset($operators) || $hash !== $newHash) {
    		$hash = $newHash;

	        if (is_null($priceList)) {
	            $bliskapaczka = $this->getMapShippingMethod();
	            $data = array(
	                "parcel" => array(
	                    'dimensions' => $this->getParcelDimensions($bliskapaczka->settings)
	                )
	            );
	            if ($is_cod === true) {
	                $data['codValue'] = $cart_total;
	            }
	            $priceList = $this->getPriceList($data);
	            if (! is_array( $priceList) ) {
	            	$priceList = [];
	            }
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
    	}
        return json_encode($operators);
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pricing
     */
    public function getApiClientPricing()
    {
        return new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing(
        	$this->getApiKey(),
            $this->getApiMode()
        );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pos
     */
    public function getApiClientPos()
    {
        return new \Bliskapaczka\ApiClient\Bliskapaczka\Pos(
        	$this->getApiKey(),
            $this->getApiMode()
        );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order
     */
    public function getApiClientOrder()
    {
    	return new \Bliskapaczka\ApiClient\Bliskapaczka\Order(
        	$this->getApiKey(),
            $this->getApiMode()
        );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice
     */
    public function getApiClientOrderAdvice()
    {
        return new \Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice(
        	$this->getApiKey(),
            $this->getApiMode()
        );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Todoor\Advice
     */
    public function getApiClientTodoorAdvice()
    {
        return new \Bliskapaczka\ApiClient\Bliskapaczka\Todoor\Advice(
        	$this->getApiKey(),
            $this->getApiMode()
        );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order\Pickup
     */
    public function getApiClientPickup()
    {
        return new \Bliskapaczka\ApiClient\Bliskapaczka\Order\Pickup(
        	$this->getApiKey(),
            $this->getApiMode()
        );
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
     * Returns a url to waybill for given bliskapaczka order id
     * 
     * @param string $order_id Bliskapaczka order id
     * @throws \InvalidArgumentException
     *  
     * @return array of waybills urls 
     */
    public function getWaybillUrls(string $order_id)
    {
    	$data = [];
    	
    	$order_id = trim( $order_id );
    	
    	if ( mb_strlen($order_id) === 0) {
    		throw new \InvalidArgumentException('Invalid bliskapaczka order id');
    	}
		
		try {
	 		$api = new \Bliskapaczka\ApiClient\Bliskapaczka\Order\Waybill(
	 			$this->getApiKey(),
	 			$this->getApiMode()
	 		);

	 		$api->setOrderId($order_id);

	 		$response = json_decode($api->get(), true);
	 		
	 		if (is_array($response) && count($response) > 0) {
	 			foreach ($response as $r) {
	 				$data[] = $r['url'];
	 			}
	 		}
		} catch (\Exception $e) {
			wc_get_logger()->debug($e->getMessage(), ['bliskapczka_order_id' => $order_id]);
     	}
	 	return $data;
    }

 	/**
 	 * Returns a API Key for bliskapaczka.pl
 	 * 
 	 * @throws \Exception If key not set
 	 * return string API key
 	 */
    public function getApiKey()
    {
    	$key = $this->getMapShippingMethod()->get_option(self::API_KEY, null);

    	if ( !\is_string($key) || \mb_strlen( $key ) === 0 ) {
    		throw new \Exception('API KEY for bliskapaczka.pl was not set in configuration.');
    	}

    	return $key;
    }
    /**
     * Get API mode
     *
     * @return string 'prod' for production, 'test' for sandbox.
     */
    public function getApiMode()
    {
        $mode = 'prod';

        if ( $this->getMapShippingMethod()->get_option(self::TEST_MODE, 'no') === 'yes') {
			$mode = 'test';	
        }

        return $mode;
    }
	
    /**
     * Returns  information about sandbox api mode
     * @return boolean  TRUE is sandbox
     */
	public function isSandbox() 
	{
		return $this->getApiMode() !== 'prod';
	}

    /**
     * Returns information if auto advice is enabled.
     *
     * @return bool
     */
    public function isAutoAdvice()
    {
    	return self::FUNCTIONALITY_AUTO_ADVICE_ENABLED === true 
    			&& $this->getMapShippingMethod()->get_option( self::AUTO_ADVICE, 'no' ) === 'yes';
    }
    
    /**
     * Get string for ajax security call
     * 
     * @return string
     */
    public static function getAjaxNonce() 
    {
    	return 'bliskapaczka_nonce_ajax';
    }

    /**
     * Returns instance of map shipping method
     * 
     * @return Bliskapaczka_Map_Shipping_Method
     */
    public function getMapShippingMethod()
    {
    	if ( !isset($this->map_shipping_method) ) {
    		$this->map_shipping_method = new Bliskapaczka_Map_Shipping_Method();
    	}

    	return $this->map_shipping_method;
    }

    /**
     * Returns instance of courier shipping method
     * 
     * @return Bliskapaczka_Courier_Shipping_Method
     */
    public function getCourierShippingMethod()
    {
    	if ( !isset($this->courier_shipping_method) ) {
    		$this->courier_shipping_method = new Bliskapaczka_Courier_Shipping_Method();
    	}
    	
    	return $this->courier_shipping_method;
    }
    
    /**
     * Returns choosed shipping method id from WooCommerce order.
     * 
     * @param WC_Order $order  WooCommerce order
     * @return string|NULL shipping method id
     */
    public function getWCShipingMethodId(WC_Order $order) 
    {
    	$methods = $order->get_shipping_methods();

    	if ( count($methods) > 0 ) {
    		$method = \array_shift($methods);
    		return $method->get_method_id();
    	}

    	return null;
    }
	
    /**
     * Returns information if current method on order page is COD (cash on delivery)
     * 
     * @return boolean TRUE 
     */
    public function isChoosedPaymentCOD() 
    {
    	return 'cod' === WC()->session->get( 'chosen_payment_method', null );
    }
}
