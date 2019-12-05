<?php
/**
 * Plugin Name: Bliskapaczka.pl
 * Plugin URI: https://github.com/bliskapaczkapl/woocommerce
 * Description: Bliskapaczka.pl Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Mateusz Koszutowski
 * Text Domain: bliskapaczka-shipping-method
 *
 * @package  Bliskapaczka
 * @subpackage Woocommerce
 * @author Mateusz Koszutowski
 * @copyright Bliskapaczka
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BLISKAPACZKA_ABSPATH', dirname( __FILE__ ) . '/' );
require_once 'includes/class-bliskapaczka-shipping-method-core.php';

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	/**
	 * Bliskapaczka Shipping Method
	 */
	function bliskapaczka_map_shipping_method() {
		require_once 'includes/class-bliskapaczka-map-shipping-method.php';
	}

	/**
	 * Bliskapaczka Courier Shipping Method
	 */
	function bliskapaczka_courier_shipping_method() {
		require_once 'includes/class-bliskapaczka-courier-shipping-method.php';
	}
	add_action( 'woocommerce_shipping_init', 'bliskapaczka_map_shipping_method' );
	add_action( 'woocommerce_shipping_init', 'bliskapaczka_courier_shipping_method' );

	/**
	 * Add Bliskapaczka shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function add_bliskapaczka_shipping_method( $methods ) {
		$methods[] = 'Bliskapaczka_Map_Shipping_Method';
		return $methods;
	}

	/**
	 * Add Bliskapaczka courier shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function add_bliskapaczka_courier_shipping_method( $methods ) {
		$methods[] = 'Bliskapaczka_Courier_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_bliskapaczka_courier_shipping_method' );

	/**
	 * Add link for select parcel point displayed on checkout page.
	 *
	 * @param Bliskapaczka_Shipping_Courier_Method $method Bliskapaczka Courier Shipping method.
	 */
	function show_table( $method ) {
		if ( 'bliskapaczka-courier' === $method->id ) {
			$payment_method = WC()->session->get( 'chosen_payment_method' );
			$helper         = new Bliskapaczka_Shipping_Method_Helper();
			$price_list     = $helper->getPriceListForCourier();
			$courier        = WC()->session->get( 'bliskapaczka_posOperator' );
			echo '<div class="bliskapaczka_courier_wrapper"></div>';
			foreach ( $price_list as $item ) {
				$operator_name = $item->operator;
				$price         = $item->price->gross;
				$cod_price     = $item->cod;
				if ( 'cod' === $payment_method ) {
					$price_show = $price + $cod_price;
				} else {
					$price_show = $price;
				}
				$class = 'bliskapaczka_courier_item_wrapper';
				if ( $operator_name === $courier ) {
					$class = 'bliskapaczka_courier_item_wrapper checked';
				}
				echo '<label class="' . esc_html( $class ) . '" for="bliskapaczka_courier_posOperator" data-operator="' . esc_html( $operator_name ) . '">';
				echo '<input type="radio" name="bliskapaczka_courier_posOperator" value="' . esc_html( $operator_name ) . '">';
				echo '<div class="bliskapaczka_courier_item">';
				echo '<div class="bliskapaczka_courier_item_logo"><img src="https://bliskapaczka.pl/static/images/' . esc_html( $operator_name ) . '.png" alt="' . esc_html( $operator_name ) . '"></div>';
				echo '<div class="bliskapaczka_courier_item_price">';
				echo '<span class="bliskapaczka_courier_item_price_value" data-price="' . esc_html( $price ) . '" data-cod-price="' . esc_html( $cod_price ) . '">' . esc_html( $price_show ) . '</span><span>zł</span>';
				echo '</div>';
				echo '</div>';
				echo '</label>';
			}
		}
	}
	add_action( 'woocommerce_after_shipping_rate', 'show_table' );
	/**
	 * Add link for select parcel point displayed on checkout page.
	 *
	 * @param Bliskapaczka_Shipping_Method $method Bliskapaczka Shipping method.
	 */
	function show_map_anchorn( $method ) {
		$helper = new Bliskapaczka_Shipping_Method_Helper();

		$bliskapaczka = new Bliskapaczka_Map_Shipping_Method();

		if ( 'bliskapaczka' === $method->id && is_checkout() === true ) {
			$payment_method = WC()->session->get( 'chosen_payment_method' );
			$operators      = json_decode( $helper->getOperatorsForWidget() );
			$fedex          = json_decode( $helper->getFedexConfigurationForWidget() );
			$operators      = wp_json_encode( array_merge( $operators, $fedex ) );
			$cod_only       = 'false';
			if ( 'cod' === $payment_method ) {
				$operators = $helper->recalculatePrice( $operators );
				$cod_only  = 'true';
			}

			// @codingStandardsIgnoreStart
			echo " <a href='#bpWidget_wrapper' " .
				"onclick='Bliskapaczka.showMap(" .
					esc_html( $operators ) .
					', "' .
					esc_html( $helper->getGoogleMapApiKey( $bliskapaczka->settings ) ) .
					'", ' .
					esc_html( ( 'test' === $helper->getApiMode( $bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'] ) ? 'true' : 'false' ) ) .
                    ',' .
                    esc_html( $cod_only) .
					")'>" .
					esc_html( 'Wybierz punkt dostawy' ) . '</a>';
			// @codingStandardsIgnoreEnd
			echo '<input name="bliskapaczka_posCode" type="hidden" id="bliskapaczka_posCode" value="' . esc_html( WC()->session->get( 'bliskapaczka_posCode' ) ) . '" />';
			echo '<input name="bliskapaczka_posOperator" type="hidden" id="bliskapaczka_posOperator" value="' . esc_html( WC()->session->get( 'bliskapaczka_posOperator' ) ) . '" />';

			if ( WC()->session->get( 'bliskapaczka_posCode' ) && WC()->session->get( 'bliskapaczka_posOperator' ) ) {
				$api_client = $helper->getApiClientPos( $bliskapaczka );
				$api_client->setPointCode( WC()->session->get( 'bliskapaczka_posCode' ) );
				$api_client->setOperator( WC()->session->get( 'bliskapaczka_posOperator' ) );
				$pos_info = json_decode( $api_client->get() );
			}

			echo '<div id="bpWidget_aboutPoint" style="width: 100%; ' . ( ( ! isset( $pos_info ) ) ? ' display: none; ' : '' ) . '">';
			echo '<p>' . esc_html( __( 'Selected Point', 'bliskapaczka-shipping-method' ) ) . ': <span id="bpWidget_aboutPoint_posData">';
			if ( isset( $pos_info ) ) {
				echo '</br>' . esc_html( $pos_info->operator ) . '</br>' .
					( ( $pos_info->description ) ? esc_html( $pos_info->description ) . '</br>' : '' ) .
					esc_html( $pos_info->street ) . '</br>' .
					// @codingStandardsIgnoreStart
					( ( $pos_info->postalCode ) ? esc_html( $pos_info->postalCode ) . ' ' : '' ) . esc_html( $pos_info->city );
					// @codingStandardsIgnoreEnd
			}
			echo '</span></p>';
			echo '</div>';
		}
	}

	/**
	 * Add wrapper div for wigdet and map with parcel points.
	 *
	 * @param mixed $checkout Some data.
	 */
	function add_widget_div( $checkout ) {
		echo '<div style="">';
		echo '<div id="bpWidget_wrapper">';
		echo "<a name='bpWidget_wrapper'><a/>";
		echo '<div id="bpWidget" style="height: 600px; display: none;"></div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Add fields to manage POS code and POS operator to checkout
	 *
	 * @param array $fields Checkput fields list.
	 */
	function custom_override_checkout_fields( $fields ) {
		$fields['bliskapaczka'] = array(
			'bliskapaczka_posCode'     => array(
				'label' => __( 'POS Code', 'bliskapaczka-shipping-method' ),
				'type'  => 'text',
			),
			'bliskapaczka_posOperator' => array(
				'label' => __( 'POS Code', 'bliskapaczka-shipping-method' ),
				'type'  => 'text',
			),
		);
		return $fields;
	}

	/**
	 * For shipping method choosen by customer we should display valid price on cart page.
	 *
	 * @param mixed $packages Some data.
	 */
	function update_price_for_chosen_carrier( $packages ) {

		// @codingStandardsIgnoreStart
		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $checkout_data );
		}
		// @codingStandardsIgnoreEnd
		$pos_code     = isset( $checkout_data['bliskapaczka_posCode'] ) ? wc_clean( $checkout_data['bliskapaczka_posCode'] ) : '';
		$pos_operator = isset( $checkout_data['bliskapaczka_posOperator'] ) ? wc_clean( $checkout_data['bliskapaczka_posOperator'] ) : '';

		if ( ! $pos_code && WC()->session->get( 'bliskapaczka_posCode' ) ) {
			$pos_code = WC()->session->get( 'bliskapaczka_posCode' );
		}

		if ( ! $pos_operator && WC()->session->get( 'bliskapaczka_posCode' ) ) {
			$pos_operator = WC()->session->get( 'bliskapaczka_posOperator' );
		}

		if ( $pos_code ) {
			WC()->session->set( 'bliskapaczka_posCode', $pos_code );
		}
		if ( $pos_operator ) {
			WC()->session->set( 'bliskapaczka_posOperator', $pos_operator );
		}

		return $packages;
	}

	/**
	 * Create new order in Bliska Paczka if shipping method is bliskapaczka.
	 *
	 * @param int $order_id Order ID.
	 * @throws Exception If can't send data to bliskapaczka.
	 */
	function create_order_via_api( $order_id ) {
		if ( 0 < $order_id ) {
			$order = wc_get_order( $order_id );
		}

		// @codingStandardsIgnoreStart
		$pos_code = isset( $_POST['bliskapaczka_posCode'] ) ? wc_clean( $_POST['bliskapaczka_posCode'] ) : '';
		$pos_operator = isset( $_POST['bliskapaczka_posOperator'] ) ? wc_clean( $_POST['bliskapaczka_posOperator'] ) : '';
		// @codingStandardsIgnoreEnd
		foreach ( $order->get_items( array( 'shipping' ) ) as $item_id => $item ) {
			$shipping_item_id = $item_id;
		}

		wc_add_order_item_meta( $shipping_item_id, '_bliskapaczka_posCode', $pos_code );
		wc_add_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator', $pos_operator );

		if ( ! $order ) {
			return false;
		}

		$bliskapaczka       = new Bliskapaczka_Map_Shipping_Method();
		$helper             = new Bliskapaczka_Shipping_Method_Helper();
		$shipping_method    = array_shift( $order->get_shipping_methods() );
		$shipping_method_id = $shipping_method['method_id'];
		$mapper             = new Bliskapaczka_Shipping_Method_Mapper();
		if ( 'bliskapaczka-courier' === $shipping_method_id ) {
			$order_data = $mapper->getDataForCourier( $order, $helper, $bliskapaczka->settings );
			if ( $order->get_payment_method() === 'cod' ) {
				$order_data = $mapper->prepareCOD( $order_data, $order );
			}
			try {
				$api_client = $helper->getApiClientOrder( $bliskapaczka );
				$api_client->create( $order_data );
			} catch ( Exception $e ) {
				throw new Exception( $e->getMessage(), 1 );
			}
		}
		// TODO: "Bliskapaczka, od" to const.
		if ( 'bliskapaczka' !== $shipping_method_id ) {
			return false;
		}

		$order_data = $mapper->getData( $order, $helper, $bliskapaczka->settings );

		try {
			if ( $order->get_payment_method() === 'cod' ) {
				$order_data = $mapper->prepareCOD( $order_data, $order );
			}
			$api_client = $helper->getApiClientOrder( $bliskapaczka );
			$api_client->create( $order_data );

			WC()->session->set( 'bliskapaczka_posCode', '' );
			WC()->session->set( 'bliskapaczka_posOperator', '' );
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage(), 1 );
		}
	}

	/**
	 * Add link to settings on plugins list in admin panel.
	 *
	 * @param array $links Links list.
	 */
	function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=bliskapaczka' ) .
				'" title="' . esc_attr( __( 'View Bliskapaczka Settings', 'bliskapaczka-shipping-method' ) ) . '">' . __( 'Settings', 'bliskapaczka' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Include JS.
	 */
	function add_scripts_and_scripts() {
		$helper             = new Bliskapaczka_Shipping_Method_Helper();
		$operators          = json_decode( $helper->getOperatorsForWidget() );
		$fedex              = json_decode( $helper->getFedexConfigurationForWidget() );
		$price_list         = wp_json_encode( array_merge( $operators, $fedex ) );
		$bliskapaczka       = new Bliskapaczka_Map_Shipping_Method();
		$google_map_api_key = $helper->getGoogleMapApiKey( $bliskapaczka->settings );
		$test_mode          = ( 'test' === $helper->getApiMode( $bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'] ) ? 'true' : 'false' );
		$script             = 'var operators = ' . $price_list . '; ';
		$script            .= 'var GoogleApiKey = ' . wp_json_encode( $google_map_api_key ) . ';';
		$script            .= 'var testMode = ' . wp_json_encode( $test_mode ) . ';';

		wp_register_script( 'widget-script', 'https://widget.bliskapaczka.pl/v5/main.js', array(), 'v5', false );
		wp_enqueue_script( 'widget-script' );

		wp_register_style( 'widget-styles', 'https://widget.bliskapaczka.pl/v5/main.css', array(), 'v5', false );
		wp_enqueue_style( 'widget-styles' );
		wp_register_style( 'widget-styles-bliskapaczka', plugin_dir_url( __FILE__ ) . 'assets/css/bliskapaczka.css', array(), 'v5', false );
		wp_enqueue_style( 'widget-styles-bliskapaczka' );

		wp_register_script( 'plugin-script', plugin_dir_url( __FILE__ ) . 'assets/js/bliskapaczka.js', array(), 'v5', false );
		wp_enqueue_script( 'plugin-script' );
		wp_add_inline_script( 'plugin-script', $script, 'before' );
	}


	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'plugin_action_links' );

	add_filter( 'woocommerce_shipping_packages', 'update_price_for_chosen_carrier' );

	add_action( 'wp_enqueue_scripts', 'add_scripts_and_scripts' );

	add_filter( 'woocommerce_shipping_methods', 'add_bliskapaczka_shipping_method' );
	add_action( 'woocommerce_after_shipping_rate', 'show_map_anchorn' );
	add_action( 'woocommerce_after_checkout_form', 'add_widget_div' );

	add_action( 'woocommerce_checkout_update_order_meta', 'create_order_via_api' );

	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );


	add_action( 'woocommerce_calculate_totals', 'set_shipping_cost', 10 );

	/**
	 * Set correct price
	 *
	 * @param mixed $cart Cart from Woocommerce.
	 */
	function set_shipping_cost( $cart ) {
		$price          = get_price();
		$shipping_total = $cart->get_shipping_total();
		$cart->set_shipping_total( $shipping_total + $price );
	}

		add_filter( 'woocommerce_calculated_total', 'custom_calculated_total', 10, 2 );

	/**
	 * Recalculate total price
	 *
	 * @param float $total Total price.
	 * @param mixed $cart Object from Woocommerce.
	 *
	 * @return int
	 */
	function custom_calculated_total( $total, $cart ) {

		return $total + get_price();
	}

	/**
	 * Return price
	 *
	 * @return int
	 */
	function get_price() {
		$price                 = 0;
		$chosen_methods        = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_method         = $chosen_methods[0];
		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
        // @codingStandardsIgnoreStart
		parse_str( $_POST['post_data'], $post_data );
		$pos_operator = $post_data['bliskapaczka_posOperator'];
		$pos_code     = $post_data['bliskapaczka_posCode'];
        // @codingStandardsIgnoreEnd
		if ( 'bliskapaczka-courier' === $chosen_method ) {
			$method = new Bliskapaczka_Courier_Shipping_Method();
		}
		if ( 'bliskapaczka' === $chosen_method ) {
			$method = new Bliskapaczka_Map_Shipping_Method();
		}

		if ( ! isset( $pos_operator ) ) {
			$pos_operator = WC()->session->get( 'bliskapaczka_posoperator' );
		}
		if ( ( 'bliskapaczka-courier' === $chosen_method ) || ( 'bliskapaczka' === $chosen_method ) ) {
			if ( 'cod' === $chosen_payment_method ) {
				$is_cod = true;
			} else {
				$is_cod = false;
			}
			$price = $method->recalculate_shipping_cost( $pos_operator, $pos_code, $is_cod );

		}
		return $price;
	}
}
