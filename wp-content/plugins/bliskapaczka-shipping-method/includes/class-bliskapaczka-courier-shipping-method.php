<?php
class Bliskapaczka_Courier_Shipping_Method extends Bliskapaczka_Shipping_Method_Base {
	
    /**
     * Bliskapaczka_Courier_Shipping_Method constructor.
     */
    public function __construct() {
    	
    	$this->id                 = self::get_identity();
        $this->method_title       = __( 'Bliskapaczka Courier Shipping', 'bliskapaczka-shipping-method' );
        $this->method_description = __( 'Custom Coureir Shipping Method for Bliskapaczka', 'bliskapaczka-shipping-method' );

        $this->availability = 'including';
        $this->countries    = array(
            'PL',
        );

        $this->init();

        $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
        $this->title   = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Bliskapaczka Shipping', 'bliskapaczka-shipping-method' );
    }
    
    /**
     *
     * {@inheritDoc}
     * @see Bliskapaczka_Shipping_Method_Base::get_identity()
     */
    public static function get_identity() {
    	return 'bliskapaczka-courier';
    }

    /**
     * Init function
     */
    private function init() {
        $this->init_form_fields();
        $this->init_settings();

        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Add field to admin panel
     */
    function init_form_fields() {
        $this->form_fields = array(
            'courier_enabled' => array(
                'title'       => __( 'Enable', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'Enable this shipping method', 'bliskapaczka-shipping-method' ),
                'default'     => 'yes',
            ),
        	Bliskapaczka_Shipping_Method_Helper::TITLE_COURIER => array(
                'title'       => __( 'Delivery name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Deliver name to be display on site', 'bliskapaczka-shipping-method' ),
                'default'     => __( 'Delivery to the door', 'bliskapaczka-shipping-method' ),
            	'custom_attributes' => array( 'required' => 'required' ),
            	'class' => 'bliskapaczka_admin_field_required',
            ),
        );
    }

    /**
     * This function is used to calculate the shipping cost. Within this function we can check for weights,
     * dimensions and other parameters.
     *
     * @access public
     * @param mixed $package From Hook.
     * @return void
     */
    public function calculate_shipping( $package = array() ) {

    	
        // @codingStandardsIgnoreStart
    	$helper         = Bliskapaczka_Shipping_Method_Helper::instance();
        $bliskapaczka   = $helper->getCourierShippingMethod();

        $label = $bliskapaczka->settings[$helper::TITLE_COURIER];
        if (empty($label)) {
            $label = __( 'Delivery to the door', 'bliskapaczka-shipping-method' );
        }
        $rate = array(
            'id'       => $this->id,
            'label'    => $bliskapaczka->settings[$helper::TITLE_COURIER],
            'cost'     => 0,
            'calc_tax' => 'per_item',
        );
        // @codingStandardsIgnoreEnd
        $this->add_rate( $rate );

    }

   	/**
   	 * 
   	 * {@inheritDoc}
   	 * @see Bliskapaczka_Shipping_Method_Base::price_list()
   	 */
    public function get_price_list( $cart_total,  $is_cod = false )
    {
    	$helper = $this->helper();
    	$priceList = new Bliskapaczka_Price_List();
    	
    	$request = array(
    		'parcel' => array(
    			'dimensions' => $helper->getParcelDimensions()
    		),
    		'deliveryType' => 'D2D'
    	);
    	
    	if ( true === $is_cod) {
    		$request['codValue'] = $cart_total;
    	}
    	
    	$items = $helper->getPriceList($request);

    	if ( is_array( $items ) ) {
    		foreach ($items as $item) {
    			if ($item->availabilityStatus === true) {
    				$priceList->append( Bliskapaczka_Price_List_Item_Factory::fromApiItem( $item ) );
    			}
    		}
    	}
    	
    	return $priceList;
    }
}