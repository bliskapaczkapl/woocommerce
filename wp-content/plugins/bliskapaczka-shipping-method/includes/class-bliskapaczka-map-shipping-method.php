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
                'description' => __( 'Włącz tę metodę wysyłki', 'bliskapaczka-shipping-method' ),
                'default'     => 'yes',
            ),
            $helper::TITLE                  => array(
                'title'       => __( 'Title', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Title to be display on site', 'bliskapaczka-shipping-method' ),
                'default'     => __( 'Dostawa do punktu', 'bliskapaczka-shipping-method' ),
            ),
            $helper::API_KEY                => array(
                'title'       => __( 'API Key', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'API Key', 'bliskapaczka-shipping-method' ),
            ),
            $helper::COD_ONLY               => array(
                'title'       => __( 'COD only enabled', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'COD only enabled', 'bliskapaczka-shipping-method' ),
                'default'     => 'no',
            ),
            $helper::TEST_MODE              => array(
                'title'       => __( 'Test mode enabled', 'bliskapaczka-shipping-method' ),
                'type'        => 'checkbox',
                'description' => __( 'Test mode enabled', 'bliskapaczka-shipping-method' ),
                'default'     => 'yes',
            ),
            $helper::GOOGLE_MAP_API_KEY     => array(
                'title'       => __( 'Google Map API Key', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'API Key', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_X => array(
                'title'       => __( 'Fixed parcel type size X (cm)', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Fixed parcel type size X (cm)', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_Y => array(
                'title'       => __( 'Fixed parcel type size Y (cm)', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Fixed parcel type size X (cm)', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_Z => array(
                'title'       => __( 'Fixed parcel type size Z (cm)', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Fixed parcel type size X (cm)', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SIZE_TYPE_FIXED_SIZE_WEIGHT => array(
                'title'       => __( 'Fixed parcel type weight (kg)', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Fixed parcel type weight (kg)', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_EMAIL           => array(
                'title'       => __( 'Sender email', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender email', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_FIRST_NAME      => array(
                'title'       => __( 'Sender first name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender first name', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_LAST_NAME       => array(
                'title'       => __( 'Sender last name', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender last name', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_PHONE_NUMBER    => array(
                'title'       => __( 'Sender phone number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender phone number', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_STREET          => array(
                'title'       => __( 'Sender street', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender street', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_BUILDING_NUMBER => array(
                'title'       => __( 'Sender building numbe', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender building numbe', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_FLAT_NUMBER     => array(
                'title'       => __( 'Sender flat numbe', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender flat numbe', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_POST_CODE       => array(
                'title'       => __( 'Sender post code', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender post code', 'bliskapaczka-shipping-method' ),
            ),
            $helper::SENDER_CITY            => array(
                'title'       => __( 'Sender city', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Sender city', 'bliskapaczka-shipping-method' ),
            ),
            $helper::BANK_ACCOUNT_NUMBER    => array(
                'title'       => __( 'Bank account number', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Bank account number', 'bliskapaczka-shipping-method' ),
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
        $price_list     = $helper->getPriceList();
        $shipping_price = round( $helper->getLowestPrice( $price_list, true ), 2 );
        // @codingStandardsIgnoreStart
        $label = $bliskapaczka->settings[ $helper::TITLE ];
        if (empty($label)) {
            $label = __( 'Dostawa do punktu', 'bliskapaczka-shipping-method' );
        }
        $rate = array(
            'id'       => $this->id,
            'label'    => $label,
            'cost'     => $shipping_price,
            'calc_tax' => 'per_item',
        );
        // @codingStandardsIgnoreEnd
        $this->add_rate( $rate );

    }
}