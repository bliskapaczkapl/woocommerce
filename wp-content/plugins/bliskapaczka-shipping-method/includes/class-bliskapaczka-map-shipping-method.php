<?php
class Bliskapaczka_Map_Shipping_Method extends WC_Shipping_Method {
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->id                 = 'bliskapaczka';
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
     * Init your settings
     *
     * @access public
     * @return void
     */
    public function init() {
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
     * This function is used to calculate the shipping cost. Within this function we can check for weights,
     * dimensions and other parameters.
     *
     * @access public
     * @param mixed $package From Hook.
     * @return void
     */
    public function calculate_shipping( $package = array() ) {

        $helper         = new Bliskapaczka_Shipping_Method_Helper();
        $bliskapaczka   = new Bliskapaczka_Map_Shipping_Method();

        // @codingStandardsIgnoreStart
        $label = $bliskapaczka->settings[ $helper::TITLE ];
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
     * @param float $cart_total
     * @param string $operator_name
     * @param string $operator_code
     * @param boolean $is_cod
     *
     * @return int
     */
    public function recalculate_shipping_cost(
        $cart_total = 0.0,
        $operator_name = '',
        $operator_code = '',
        $is_cod = false
    ) {
        $helper             = new Bliskapaczka_Shipping_Method_Helper();
        $price_list         = $helper->getOperatorsForWidget($cart_total, null, $is_cod);
        $price_list = json_decode($price_list);
        $price = 0;
        foreach ($price_list as $item) {
            if ($item->operator === $operator_name) {
                $price = $item->price->gross;
                break;
            }
        }
        return $price;
    }
}