<?php

use Bliskapaczka\ApiClient\AbstractBliskapaczka;
use Bliskapaczka\ApiClient\Bliskapaczka\Order\AvailableServices;

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
    const FLEXIBLE_SHIPPING = 'BLISKAPACZKA_FLEXIBLE_SHIPPING';

    /**
     * Default service type for XPREES Curier.
     * Allowed values are LOCAL, COUNTRY, INTERNATIONAL, VOIVODESHIP, ECOMMERCE
     * 
     * @var string
     */
    const XPRESS_DEFAULT_SERVICE_TYPE = 'ECOMMERCE';
    /**
     * Temporarily blocked this functionality, because is not yet finished.
     * @var string
     */
    const FUNCTIONALITY_AUTO_ADVICE_ENABLED = true;

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
    public function getParcelDimensions()
    {
    	$bliskapaczka = $this->getMapShippingMethod();
    
        $dimensions = array(
        	"height" => $bliskapaczka->get_option(self::SIZE_TYPE_FIXED_SIZE_X),
        	"length" => $bliskapaczka->get_option(self::SIZE_TYPE_FIXED_SIZE_Y),
        	"width"  => $bliskapaczka->get_option(self::SIZE_TYPE_FIXED_SIZE_Z),
        	"weight" => $bliskapaczka->get_option(self::SIZE_TYPE_FIXED_SIZE_WEIGHT)
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
    	$key = $this->getMapShippingMethod()->get_option( self::GOOGLE_MAP_API_KEY, null );
    	
    	return null === $key 
    		? self::DEFAULT_GOOGLE_API_KEY 
    		: $key;
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
     * Get operators and prices from Bliskapaczka API
     *
     * @param array|null $data
     *
     * @return string
     */
    public function getPriceList(array $data = null)
    {
        if (is_null($data)) {
            $data = array(
              "parcel" => array(
                  'dimensions' => $this->getParcelDimensions()
              )
            );
        }

        // We generete hash for the request data, to remember response from api. 
        $hash = \md5( \json_encode( $data ) );
        
        // Take data, if it's not buffered.
        if ( ! isset( $this->buf_price_list[ $hash ]) ) {
        	$priceList = $this->getApiClientPricing()->get($data);
        	$this->buf_price_list[ $hash ] = json_decode($priceList);
        }
		
    	return $this->buf_price_list[ $hash ];
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pricing
     */
    public function getApiClientPricing()
    {
        return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Pos
     */
    public function getApiClientPos()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Pos(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order
     */
    public function getApiClientOrder()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Order(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice
     */
    public function getApiClientOrderAdvice()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Todoor\Advice
     */
    public function getApiClientTodoorAdvice()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Todoor\Advice(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }

    /**
     * Get Bliskapaczka API Client
     *
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order\Pickup
     */
    public function getApiClientPickup()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Order\Pickup(
        	$this->getApiKey(),
            $this->getApiMode()
        ) );
    }
    
    /**
     * Get Bliskapaczka Available Services API Client
     * 
     * @return \Bliskapaczka\ApiClient\Bliskapaczka\Order\AvailableServices
     */
    public function getApiClientAvailableServices()
    {
    	return $this->decorate_bp_api_request( new \Bliskapaczka\ApiClient\Bliskapaczka\Order\AvailableServices(
    		$this->getApiKey(),
    		$this->getApiMode()
    	));
    }
	
    /**
     * Decorate API request by appedn special information
     * 
     * @param AbstractBliskapaczka $client
     * @return \Bliskapaczka\ApiClient\AbstractBliskapaczka
     */
    private function decorate_bp_api_request( AbstractBliskapaczka $client ) 
    {
    	$v = new Bliskapaczka\ApiClient\ShopVersion\Woocommerce();
    	
    	// append shop engine and version
    	$client
    		->setShopName( 'woocommerce' )
    		->setShopVersion( $v->getShopVersion() );
    	
    	return $client;
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
     * Get inforamation about delivery point 
     * 
     * @param string $operator Operator indentity (UPS, DPD, ...)
     * @param string $code Point identity
     * 
     * @return \stdClass
     */
    public function getPosInfo( $operator, $code )
    {
    	$api = $this->getApiClientPos();
    	$api->setPointCode( $code );
    	$api->setOperator( $operator );
    	return json_decode( $api->get() );
    }
    
    /**
     * Convert point delivery information from api to string representations;
     * 
     * @param \stdClass $pos_info Object returned by Bliskapaczka_Shipping_Method_Helper::getPosInfo()
     * 
     * @throws InvalidArgumentException
     * @return string
     */
    public function convertPosIntoString( $pos_info ) 
    {
    	if ( ! ( $pos_info instanceof \stdClass ) ) {
    		throw new InvalidArgumentException('Excepted the stdClass ');
    	}

    	return implode( ' ', [ $pos_info->description, $pos_info->street, $pos_info->postalCode, $pos_info->city ] );
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
     * Return information if Flexible Shipping integretation is enabled
     * 
     * @return boolean  TRUE only when Flexible Shipping integretation is enabled
     */
    public function isFlexibleShippingIntegrationEnabled()
    {
    	return $this->getMapShippingMethod()->get_option( self::FLEXIBLE_SHIPPING, 'no' ) === 'yes';
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

    /**
     * Append bliskapaczka shipping method to WooCommerce avaible methods
     * 
     * @param Bliskapaczka_Shipping_Method_Base $method Instance of bliskapaczka shipping method to append.
     * @param string[] $methods  List of shipping methods.
     * 
     * @return array List of shipping methods.
     */
    public function append_to_wc_methods( $method, array $methods )
    {
    	if ( ! ( $method instanceof Bliskapaczka_Shipping_Method_Base ) ) {
    		throw new Bliskapaczka_Exception('Method argument must be instance of "Bliskapaczka_Shipping_Method_Base" class.');
    	}
    	
    	$name_parts =  explode( '\\', get_class( $method ) );
    	$method_name =  end( $name_parts );
    
    	// Always append in admin panel.
    	if ( is_admin() ) {
    		$methods[$method::get_identity()] = $method_name;
    		return $methods; 
    	}
    	
    	// Skip if method is disabled.
    	if ( ! $method->is_enabled() ) {
    		return $methods;
    	}
    	
    	// Skip if Flexible Shipping Intergration is enabled
    	if ( Bliskapaczka_Flexible_Shipping_Integration::is_integration_enabled() ) {
    		return $methods;
    	}
    	
    	// Verify API Key configuration.
    	try {
    		$this->getApiKey();
    	} catch (\Exception $e) {
    		// Api key is not setted, so we log warning and don't append.
    		wc_get_logger()->warning( $e->getMessage() );
    		return $methods;
    	}
    	
    	// Verify if any courier is enabled for this method. 
    	$price_list = $method->get_price_list(0, false);

    	if ( count( $price_list ) > 0 ) {
    		$methods[$method::get_identity()] = $method_name;
    	} else {
    		wc_get_logger()->warning( ' Operators with avaible status not found for  "' . $method->id . '" method. Please verify your configuration in bliskapaczka panel and settings in WooCommerce.' );
    	}

    	return $methods;
    }
    
    /**
     * Remember settings for shipping item in order
     * 
     * @param integer $shipping_item_id ID od shippping item
     * @param string $operator Identity of operator ( courier )
     * @param string $pos_code"null Identtity of point deleivery
     */
    public function remember_shipping_item_data( $shipping_item_id, $operator, $pos_code )
    {
    	if ( isset( $pos_code ) ) {
    		$pos_info = $this->getPosInfo( $operator, $pos_code );
    		$pos_detailed = $this->convertPosIntoString($pos_info);
    		
    		wc_add_order_item_meta( $shipping_item_id, '_bliskapaczka_posInfo', $pos_detailed );
    		wc_add_order_item_meta( $shipping_item_id, '_bliskapaczka_posCode', $pos_code );
    	}
    	
    	wc_add_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator', $operator );
    }
    
    /**
     * Process send order to api
     * 
     * @param WC_Order $order 
     * @param boolean $delivery_to_point TRUE if delivery method is delivery to point FALSE otherwise.
     * 
     * @throws Exception
     */
    public function send_order_to_api( WC_Order $order, $delivery_to_point)
    {
    	
    	$mapper = new Bliskapaczka_Shipping_Method_Mapper();
    	$bliskapaczka = $this->getMapShippingMethod();
    	$bliskapaczka->init_settings();
    	
    	// delivery to door
    	if (false === $delivery_to_point) {
    		$order_data = $mapper->getDataForCourier( $order, $this, $bliskapaczka->settings );
    		$need_to_pickup = true;
    		$advice_api_client = $this->getApiClientTodoorAdvice();
    		
		// delivery to point
    	} else {
    		$order_data = $mapper->getData( $order, $this, $bliskapaczka->settings );
    		$need_to_pickup = false;
    		$advice_api_client = $this->getApiClientOrderAdvice();
    	}
    	
    	// set cod data if required
    	if ( $order->get_payment_method() === 'cod' ) {
    		$order_data = $mapper->prepareCOD( $order_data, $order );
    		$order_data = $mapper->prepareInsuranceDataIfNeeded( $order_data, $order );
    	}
    	try {
    		if ('XPRESS' === $order_data['operatorName']) {
    			$order_data['serviceType'] = self::XPRESS_DEFAULT_SERVICE_TYPE;
	    		$avaibleServices = json_decode($this->getApiClientAvailableServices()->get($order_data), true);
	    		
	    		if ( ! is_array($avaibleServices) || 0 == count($avaibleServices) ) {
	    			
	    			$order->update_meta_data('_bliskapaczka_msg_warn', __('Dear Seller, Xpress courier are not avaible from technical reason.', 'bliskapaczka-pl'));
	    			$order->save(); 
	    			return;
	    		}
	    		
	    		// We choose the first service.
	    		$order_data['serviceId'] = $avaibleServices[0];
	    		
    		} 
    	} catch (\Exception $e) {
    		$order->update_meta_data('_bliskapaczka_msg_warn', __('Dear Seller, Xpress courier are not avaible from technical reason. Details: ', 'bliskapaczka-pl') . $e->getMessage() );
    			$order->save();
    			return;
    	}
    	
    	try {
	    	$api_client = $this->getApiClientOrder();
	    	$result     = $api_client->create( $order_data );
	    	$order_number = json_decode( $result, true )['number'];
	    	
	    	$order->update_meta_data( '_bliskapaczka_order_id', $order_number);
	    	$order->save();
	    	
	    	if ( $this->isAutoAdvice() === true ) {
	    		
	    		$advice_api_client->setOrderId( $order_number );
				$order_data = $mapper->forceAutoPickupFlag($order_data, $need_to_pickup);
	    		$advice_api_client->create( $order_data , ['bp-check-price: false']);
	    		
	    		$order->update_meta_data( '_bliskapaczka_need_to_pickup', $need_to_pickup );
	    		$order->save();
	    		
	    		$this->request_auto_pickup($order);
	    	}
	    	
	    } catch ( \Exception $e ) {
	    	
    		throw new Exception( $e->getMessage(), 1 );
	    }
	    
    }
    
    /**
     * Create auto pickup.
     * 
     * Order must have _bliskapaczka_need_to_pickup, _bliskapaczka_order_id in metda data.
     *  
     * @param WC_Order $order
     */
    public function request_auto_pickup(WC_Order $order)
	{
		$logger = wc_get_logger();

		$bliskapczka_order_id = $order->get_meta('_bliskapaczka_order_id');

		if (false == $order->get_meta('_bliskapaczka_need_to_pickup')) {
			return;
		}

		$mapper = new Bliskapaczka_Shipping_Method_Mapper();
		$bliskapaczka = $this->getMapShippingMethod();
		$bliskapaczka->init_settings();

		// Auto order pickup when auto advice is enabled WIW-127.
		$api_pickup = $this->getApiClientPickup();

		try {
			$pickup_params = $mapper->prepareDataForPickup($bliskapaczka, [
				$bliskapczka_order_id
			], true);
			$api_pickup_response = json_decode($api_pickup->create($pickup_params), true);

			if (isset($api_pickup_response['id']) && isset($api_pickup_response['number'])) {

				$order->update_meta_data('_bliskapaczka_need_to_pickup', false);
				$order->update_meta_data('_bliskapaczka_pickup_id', $api_pickup_response['id']);
				$order->update_meta_data('_bliskapaczka_pickup_number', $api_pickup_response['number']);
				$order->save();
			} else {
				$logger->error('BLISKAPACZKA: Order pickup faild: Undefined resposne from API', [
					'order_id' => $order->id,
					'_bliskapaczka_order_id' => $bliskapczka_order_id,
					'response' => $api_pickup_response
				]);
			}
		} catch (\Exception $e) {

			$logger->error('BLISKAPACZKA: Order pickup faild: ' . $e->getMessage(), [
				'order_id' => $order->id,
				'_bliskapaczka_order_id' => $bliskapczka_order_id,
				'response' => $api_pickup_response
			]);
		}
	}
}
