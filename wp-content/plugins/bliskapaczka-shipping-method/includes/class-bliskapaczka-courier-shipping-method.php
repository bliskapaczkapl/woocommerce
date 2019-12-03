<?php
class Bliskapaczka_Courier_Shipping_Method extends Bliskapaczka_Map_Shipping_Method {
    /**
     * Bliskapaczka_Courier_Shipping_Method constructor.
     */
    public function __construct() {
        $this->id                 = 'bliskapaczka-courier';
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
     * Init function
     */
    function init() {
        $this->init_form_fields();
        $this->init_settings();

        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Add field to admin panel
     */
    function init_form_fields() {
        $helper            = new Bliskapaczka_Shipping_Method_Helper();
        $this->form_fields = array(

            $helper::TITLE_COURIER => array(
                'title'       => __( 'Title', 'bliskapaczka-shipping-method' ),
                'type'        => 'text',
                'description' => __( 'Title to be display on site', 'bliskapaczka-shipping-method' ),
                'default'     => __( 'Dostawa do drzwi', 'bliskapaczka-shipping-method' ),
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
        $helper         = new Bliskapaczka_Shipping_Method_Helper();
        $bliskapaczka   = new Bliskapaczka_Courier_Shipping_Method();
        $price_list = $helper->getPriceListForCourier();
        $shipping_price = round( $helper->getLowestPrice( $price_list, true ), 2 );

        $label = $bliskapaczka->settings[$helper::TITLE_COURIER];
        if (empty($label)) {
            $label = __( 'Dostawa do drzwi', 'bliskapaczka-shipping-method' );
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
     * @param string $operator_name
     * @param string $operator_code
     * @param boolean $is_cod
     *
     * @return int
     */
    public function recalculate_shipping_cost($operator_name = '', $operator_code = '', $is_cod = false) {
        $helper         = new Bliskapaczka_Shipping_Method_Helper();
        $price_list = $helper->getPriceListForCourier();
        $price = 0;
        foreach ($price_list as $item) {
            if ($item->operator === $operator_name) {
                $price = $item->price->gross;
                if ($is_cod === true) {
                    $price = $price + $item->cod;
                }
            }
        }
        return $price;
    }
}