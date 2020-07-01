<?php
class Bliskapaczka_Map_Shipping_Method extends Bliskapaczka_Shipping_Method_Base {
	
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct() {
    	$this->id                 = self::get_identity();
        $this->method_title       = __( 'Bliskapaczka Shipping', 'bliskapaczka-shipping-method' );
        $this->method_description = __( 'Custom Shipping Method for Bliskapaczka', 'bliskapaczka-shipping-method' );

        $this->availability = 'including';
        $this->countries    = array(
            'PL',
        );
        $this->init();

        $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
        $this->title   = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Bliskapaczka Shipping', 'bliskapaczka-shipping-method' );
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }
    
    /**
     *
     * {@inheritDoc}
     * @see Bliskapaczka_Shipping_Method_Base::get_identity()
     */
    public static function get_identity() {
    	return 'bliskapaczka';
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    private function init() {
        $this->init_form_fields();
        $this->init_settings();
    }

    /**
     * Define settings field for this shipping
     *
     * @return void
     */
    public function init_form_fields() {
        $helper            = new Bliskapaczka_Shipping_Method_Helper();
        $this->form_fields = array(
            'enabled'                       => array(
                'title'       => __( 'Enable', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'Enable this shipping method', 'bliskapaczka-shipping-method' ),
                'default'     => 'yes',
            ),
            $helper::TITLE                  => array(
                'title'       => __( 'Delivery name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Deliver name to be display on site', 'bliskapaczka-shipping-method' ),
                'default'     => __( 'Delivery to point', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::API_KEY                => array(
                'title'       => __( 'API Key', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'The API key from www.bliskapaczka.pl panel', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
/*
            $helper::AUTO_ADVICE            => array(
                'title'       => __( 'Auto advice enabled', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'Sending the order to the service Bliskapaczka.pl, where the status "Ready to send" will be automatically set', 'bliskapaczka-shipping-method' ),
                'default'     => 'no',
            ),
*/
            $helper::TEST_MODE              => array(
                'title'       => __( 'Test mode enabled', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'Required to connect with www.sandbox-bliskapaczka.pl', 'bliskapaczka-shipping-method' ),
                'default'     => 'yes',
            ),
            $helper::GOOGLE_MAP_API_KEY     => array(
                'title'       => __( 'Google Map API Key', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
			array(
				'title' 	=> __( 'Dimensions and weight of the shipment', 'bliskapaczka-shipping-method' ),
				'type' 		=> 'title',
			),
            $helper::SIZE_TYPE_FIXED_SIZE_X => array(
                'title'       => __( 'Height', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Enter the parcel height (cm)', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_Y => array(
                'title'       => __( 'Length', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Enter the parcel length (cm)', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_Z => array(
                'title'       => __( 'Width', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Enter the parcel width (cm)', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_WEIGHT => array(
                'title'       => __( 'Weight', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Enter the parcel weight (kg)', 'bliskapaczka-shipping-method' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
			array(
				'title' 	=> __( 'Sender data', 'bliskapaczka-shipping-method' ),
				'type' 		=> 'title',
			),
            $helper::SENDER_EMAIL           => array(
                'title'       => __( 'Email', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_FIRST_NAME      => array(
                'title'       => __( 'First name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_LAST_NAME       => array(
                'title'       => __( 'Last name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_PHONE_NUMBER    => array(
                'title'       => __( 'Phone number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_STREET          => array(
                'title'       => __( 'Street', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_BUILDING_NUMBER => array(
                'title'       => __( 'Building number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_FLAT_NUMBER     => array(
                'title'       => __( 'Flat number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
            ),
            $helper::SENDER_POST_CODE       => array(
                'title'       => __( 'Zip', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
            $helper::SENDER_CITY            => array(
                'title'       => __( 'City', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
			$helper::BANK_ACCOUNT_NUMBER    => array(
                'title'       => __( 'Bank account number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'custom_attributes' => array( 'required' => 'required' ),
                'class' => 'bliskapaczka_admin_field_required',
            ),
        );

    }

    /**
     * Validations of admin options fields. Required attribute is delivered through HTML5 required attribute.
     * @access public
     * @return string
     */
    public function validate_BLISKAPACZKA_SENDER_EMAIL_field($key, $value)
    {
        if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $value) || strlen($value) > 60) {
            WC_Admin_Settings::add_error(esc_html__('E-mail is invalid or longer than 60 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_FIRST_NAME_field($key, $value)
    {
        if (strlen($value) > 30) {
            WC_Admin_Settings::add_error(esc_html__('First name is longer than 30 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return ($value);
    }

    public function validate_BLISKAPACZKA_SENDER_LAST_NAME_field($key, $value)
    {
        if (strlen($value) > 30) {
            WC_Admin_Settings::add_error(esc_html__('Last name is logner than 30 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_PHONE_NUMBER_field($key, $value)
    {
        $value = preg_replace(array("/\s+/", "/-/"), "", $value);
        if (!preg_match("/^\d{9}$/", $value)) {

            WC_Admin_Settings::add_error(esc_html__('Phone number is invalid (only 9 letters phone numbers are allowed).', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_STREET_field($key, $value)
    {
        if (strlen($value) > 30) {
            WC_Admin_Settings::add_error(esc_html__('Street name cannot exceed 30 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_BUILDING_NUMBER_field($key, $value)
    {
        if (strlen($value) > 10) {
            WC_Admin_Settings::add_error(esc_html__('Building number cannot exceed 10 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_FLAT_NUMBER_field($key, $value)
    {
        if (strlen($value) > 10) {
            WC_Admin_Settings::add_error(esc_html__('Flat number cannot exceed 10 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_POST_CODE_field($key, $value)
    {
        if (strlen($value) > 10) {
            WC_Admin_Settings::add_error(esc_html__('Post code cannot exceed 10 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_SENDER_CITY_field($key, $value)
    {
        if (strlen($value) > 30) {
            WC_Admin_Settings::add_error(esc_html__('City name cannot exceed 30 characters.', 'bliskapaczka-shipping-method'));
            return;
        }
        return $value;
    }

    public function validate_BLISKAPACZKA_BANK_ACCOUNT_NUMBER_field($key, $value)
    {
        $value = preg_replace(array("/\s+/", "/-/", "/PL/", "/pl/"), "", $value);
        $iban = 'PL' . $value;
        $myIban = new IBAN($iban);
        if ($myIban->Verify() === false) {
            WC_Admin_Settings::add_error(esc_html__('Bank account is not valid IBAN number.', 'bliskapaczka-shipping-method'));
            return;
        } else {
            return $value;
        }
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
        $label = $this->get_option( Bliskapaczka_Shipping_Method_Helper::TITLE );
        if (empty($label)) {
            $label = __( 'Delivery to point', 'bliskapaczka-shipping-method' );
        }
        $rate = array(
            'id'       => $this->id,
            'label'    => $label,
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
				'dimensions' => $helper->getParcelDimensions($this->settings)
			),
			'deliveryType' => 'P2P'
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

		$request['deliveryType'] = 'D2P';

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