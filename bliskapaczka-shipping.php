<?php
/**
 * Plugin Name: Bliskapaczka Shipping
 * Plugin URI: https://github.com/bliskapaczkapl/woocommerce
 * Description: Custom Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Mateusz Koszutowski
 * Text Domain: Bliskapaczka
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BLISKAPACZKA_ABSPATH', dirname( __FILE__ ) . '/' );
include_once( 'includes/class-bliskapaczka-shipping-method-core.php' );

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function bliskapaczka_shipping_method() {
		if ( ! class_exists( 'Bliskapaczka_Shipping_Method' ) ) {
			class Bliskapaczka_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'bliskapaczka';
					$this->method_title       = __( 'Bliskapaczka Shipping', 'bliskapaczka' );
					$this->method_description = __( 'Custom Shipping Method for Bliskapaczka', 'bliskapaczka' );

					$this->availability = 'including';
					$this->countries = array(
						'PL',
					);

					$this->init();

					$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
					$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Bliskapaczka Shipping', 'bliskapaczka' );
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				public function init() {
					// Load the settings API
					$this->init_form_fields();
					// $this->init_settings();

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * Define settings field for this shipping
				 *
				 * @return void
				 */
				public function init_form_fields() {

					// array(
					// 'type' => 'text',
					// 'label' => $this->l('API Key'),
					// 'name' => Configuration::get(Bliskapaczka\Prestashop\Core\Helper::API_KEY),
					// 'required' => true
					// ),
					// array(
					// 'type' => 'switch',
					// 'label' => $this->l('Test mode enabled'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::TEST_MODE,
					// 'is_bool' => true,
					// 'values' => array(
					// array(
					// 'id' => 'active_on',
					// 'value' => 1,
					// 'label' => $this->l('Enabled')
					// ),
					// array(
					// 'id' => 'active_off',
					// 'value' => 0,
					// 'label' => $this->l('Disabled')
					// )
					// ),
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Fixed parce type size X (cm)'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SIZE_TYPE_FIXED_SIZE_X
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Fixed parce type size Y (cm)'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SIZE_TYPE_FIXED_SIZE_Y
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Fixed parce type size Z (cm)'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SIZE_TYPE_FIXED_SIZE_Z
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Fixed parce type weight (kg)'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SIZE_TYPE_FIXED_SIZE_WEIGHT
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender email'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_EMAIL
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender first name'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_FIRST_NAME
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender last name'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_LAST_NAME
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender phone number'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_PHONE_NUMBER
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender street'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_STREET
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender building numbe'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_BUILDING_NUMBER
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender flat numbe'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_FLAT_NUMBER
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender post code'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_POST_CODE
					// ),
					// array(
					// 'type' => 'text',
					// 'label' => $this->l('Sender city'),
					// 'name' => Bliskapaczka\Prestashop\Core\Helper::SENDER_CITY
					// )
					$this->form_fields = array(
						'enabled' => array(
							'title' => __( 'Enable', 'bliskapaczka' ),
							'type' => 'checkbox',
							'description' => __( 'Włącz tę metodę wysyłki', 'bliskapaczka' ),
							'default' => 'yes',
						),
						'title' => array(
							'title' => __( 'Title', 'bliskapaczka' ),
							'type' => 'text',
							'description' => __( 'Title to be display on site', 'bliskapaczka' ),
							'default' => __( 'Bliskapaczka Shipping', 'bliskapaczka' ),
						),
					);
				}

				/**
				 * This function is used to calculate the shipping cost. Within this function we can check for weights,
				 * dimensions and other parameters.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					$helper = new Bliskapaczka_Shipping_Method_Helper();
					$apiClient = $helper->getApiClient();
					$priceList = $apiClient->getPricing(
						array("parcel" => array('dimensions' => $helper->getParcelDimensions()))
					);
					$shippingPrice = round($helper->getLowestPrice(json_decode($priceList), true), 2);

					$rate = array(
						'id'       => $this->id,
						'label'    => 'Bliskapaczka, od',
						'cost'     => $shippingPrice,
						'calc_tax' => 'per_item',
					);

					// Register the rate
					$this->add_rate( $rate );

				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'bliskapaczka_shipping_method' );

	function add_bliskapaczka_shipping_method( $methods ) {
		$methods[] = 'Bliskapaczka_Shipping_Method';
		return $methods;
	}

	function show_map_anchorn( $method ) {
		$helper = new Bliskapaczka_Shipping_Method_Helper();

		if ( $method->id == 'bliskapaczka' ) {
			echo " <a href='#bpWidget_wrapper' " .
				"onclick='Bliskapaczka.showMap(" . $helper->getOperatorsForWidget() . ", \"AIzaSyCUyydNCGhxGi5GIt5z5I-X6hofzptsRjE\")'>" .
				'Select delivery point</a>';
			echo '<input name="bliskapaczka_posCode" type="hidden" id="bliskapaczka_posCode" value="' . WC()->session->get( 'bliskapaczka_posCode') . '" />';
			echo '<input name="bliskapaczka_posOperator" type="hidden" id="bliskapaczka_posOperator" value="' . WC()->session->get( 'bliskapaczka_posOperator') . '" />';

			echo '<div id="bpWidget_aboutPoint" style="width: 100%; display: none;">';
			echo '<p>Selected Point: <span id="bpWidget_aboutPoint_posData"></span></p>';
			echo '</div>';
		}
	}

	function add_widget_div( $checkout ) {
		echo '<div style="">';
		echo '<div id="bpWidget_wrapper">';
		echo "<a name='bpWidget_wrapper'><a/>";
		echo '<div id="bpWidget" style="height: 600px; width: 800px; display: none;"></div>';
		echo '</div>';
		echo '</div>';
	}

	// Our hooked in function - $fields is passed via the filter!
	function custom_override_checkout_fields( $fields ) {
		$fields['bliskapaczka'] = array(
			'bliskapaczka_posCode' => array(
				'label' => __( 'POS Code', 'woocommerce' ),
				'type' => 'text',
			),
			'bliskapaczka_posOperator' => array(
				'label' => __( 'POS Code', 'woocommerce' ),
				'type' => 'text',
			),
		);
		$fields['bliskapaczka'];

		return $fields;
	}

	function update_price_for_chosen_carrier($packages)
    {
    	parse_str($_POST['post_data'], $checkoutData);
    	$posCode = isset( $checkoutData['bliskapaczka_posCode'] ) ? wc_clean( $checkoutData['bliskapaczka_posCode'] ) : '';
		$posOperator = isset( $checkoutData['bliskapaczka_posOperator'] ) ? wc_clean( $checkoutData['bliskapaczka_posOperator'] ) : '';

		if ($posCode && $posOperator) {
			WC()->session->set( 'bliskapaczka_posCode', $posCode);
			WC()->session->set( 'bliskapaczka_posOperator', $posOperator);

			$helper = new Bliskapaczka_Shipping_Method_Helper();
			$apiClient = $helper->getApiClient();
			$priceList = $apiClient->getPricing(
				array("parcel" => array('dimensions' => $helper->getParcelDimensions()))
			);
			$shippingPrice = round($helper->getPriceForCarrier(json_decode($priceList), $posOperator, true), 2);

			$packages[0]['rates']['bliskapaczka']->label = 'Bliskapaczka';
	    	$packages[0]['rates']['bliskapaczka']->cost = $shippingPrice;
	    }

        return $packages;
    }

	function create_order_via_api( $order_id, $data ) {
		if ( 0 < $order_id ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return false;
		}

		if ( $order->get_shipping_method() != 'Bliskapaczka' && $order->get_shipping_method() != 'Bliskapaczka, od' ) {
			return false;
		}
		/* @var Bliskapaczka_Shipping_Method_Helper $helper */
		$helper = new Bliskapaczka_Shipping_Method_Helper();

		/* @var Bliskapaczka_Shipping_Method_Mapper $mapper */
		$mapper = new Bliskapaczka_Shipping_Method_Mapper();
		$orderData = $mapper->getData( $order, $helper );

		try {
			/* @var $apiClient \Bliskapaczka\ApiClient\Bliskapaczka */
			$apiClient = $senditHelper->getApiClient();
			$apiClient->createOrder( $orderData );
		} catch ( Exception $e ) {
			// Mage::throwException($senditHelper->__($e->getMessage()), 1);
		}
	}

	/**
	 * Include JS
	 */
	function add_scripts_and_scripts() {
		wp_register_script( 'widget-script', 'https://widget.bliskapaczka.pl/v3.2/main.js' );
		wp_enqueue_script( 'widget-script' );

		wp_register_style( 'widget-styles', 'https://widget.bliskapaczka.pl/v3.2/main.css' );
		wp_enqueue_style( 'widget-styles' );

		wp_register_script( 'plugin-script', plugin_dir_url( __FILE__ ) . 'assets/js/bliskapaczka.js' );
		wp_enqueue_script( 'plugin-script' );
	}

	add_filter('woocommerce_shipping_packages', 'update_price_for_chosen_carrier');

	add_action( 'wp_enqueue_scripts', 'add_scripts_and_scripts' );

	add_filter( 'woocommerce_shipping_methods', 'add_bliskapaczka_shipping_method' );
	add_action( 'woocommerce_after_shipping_rate', 'show_map_anchorn' );
	add_action( 'woocommerce_after_checkout_form', 'add_widget_div' );

	add_action( 'woocommerce_checkout_update_order_meta', 'create_order_via_api' );

	add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
}
