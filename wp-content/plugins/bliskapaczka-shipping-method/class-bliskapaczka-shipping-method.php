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
		if ( is_admin() !== false ) {
			$methods[] = 'Bliskapaczka_Map_Shipping_Method';
			return $methods;
		}
		$bliskapaczka = new Bliskapaczka_Map_Shipping_Method();
		if ( empty( $bliskapaczka->settings['BLISKAPACZKA_API_KEY'] ) ) {
			return $methods;
		}
		if ( 'no' === $bliskapaczka->settings['enabled'] ) {
			return $methods;
		}
		$helper    = new Bliskapaczka_Shipping_Method_Helper();
		$operators = json_decode( $helper->getOperatorsForWidget( 0.0 ) );

		if ( count( $operators ) !== 0 ) {
			$methods[] = 'Bliskapaczka_Map_Shipping_Method';
		}

		return $methods;
	}

	/**
	 * Add Bliskapaczka courier shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function add_bliskapaczka_courier_shipping_method( $methods ) {
		if ( is_admin() !== false ) {
			$methods[] = 'Bliskapaczka_Courier_Shipping_Method';
			return $methods;
		}
		$bliskapaczka = new Bliskapaczka_Map_Shipping_Method();
		if ( empty( $bliskapaczka->settings['BLISKAPACZKA_API_KEY'] ) ) {
			return $methods;
		}
		$bliskapaczka = new Bliskapaczka_Courier_Shipping_Method();
		if ( 'no' === $bliskapaczka->settings['courier_enabled'] ) {
			return $methods;
		}

		$helper     = new Bliskapaczka_Shipping_Method_Helper();
		$price_list = $helper->getPriceListForCourier( 0.0 );

		if ( count( $price_list ) !== 0 ) {
			$methods[] = 'Bliskapaczka_Courier_Shipping_Method';
		}

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

			$payment_method         = WC()->session->get( 'chosen_payment_method' );
			$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];
			$reset_selection        = false;

			if ( 'bliskapaczka' === $chosen_shipping_method ) {
				$reset_selection = true;
			}
			$cod_only = false;
			if ( 'cod' === $payment_method ) {
				$cod_only = true;
			}
			// On the cart page, we must show prices without COD (the smallest price). See JIRA WIW-222.
			if ( true === is_cart() ) {
				$cod_only = false;
			}

			$helper     = new Bliskapaczka_Shipping_Method_Helper();
			$price_list = $helper->getPriceListForCourier(
				WC()->cart->get_cart_contents_total(),
				null,
				$cod_only
			);
			$is_courier = false;
			$courier    = WC()->session->get( 'bliskapaczka_posOperator' );
			foreach ( json_decode( $price_list )as $item ) {
				if ( $courier === $item->operator ) {
					$is_courier = true;
					break;
				}
			}
			echo '<div class="bliskapaczka_courier_wrapper">';
			foreach ( json_decode( $price_list ) as $i => $item ) {
				$operator_name = $item->operator;
				$price_show    = $item->price->gross;
				$class         = 'bliskapaczka_courier_item_wrapper';
				if ( $operator_name === $courier && false === $reset_selection ) {
					$class = 'bliskapaczka_courier_item_wrapper checked';
				}
				if ( false === $reset_selection && false === $is_courier && 0 === $i ) {
					$class = 'bliskapaczka_courier_item_wrapper checked';
				}
				echo '<label class="' . esc_html( $class ) . '" for="bliskapaczka_courier_posOperator" data-operator="' . esc_html( $operator_name ) . '">';
				echo '<input type="radio" name="bliskapaczka_courier_posOperator" value="' . esc_html( $operator_name ) . '">';
				echo '<div class="bliskapaczka_courier_item">';
				echo '<div class="bliskapaczka_courier_item_logo"><img src="https://bliskapaczka.pl/static/images/' . esc_html( $operator_name ) . '.png" alt="' . esc_html( $operator_name ) . '" style="height: 25px; width: auto"></div>';
				echo '<div class="bliskapaczka_courier_item_price">';
				echo '<span class="bliskapaczka_courier_item_price_value">' . esc_html( $price_show ) . '</span><span>zł</span>';
				echo '</div>';
				echo '</div>';
				echo '</label>';
			}
			$shipping_methods = WC()->shipping->get_shipping_methods();
			if ( null === $shipping_methods['bliskapaczka'] ) {
				echo '<input name="bliskapaczka_posOperator" type="hidden" id="bliskapaczka_posOperator" value="' . esc_html( WC()->session->get( 'bliskapaczka_posOperator' ) ) . '" />';
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

			$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];
			$payment_method         = WC()->session->get( 'chosen_payment_method' );
			$cod_only               = false;
			if ( 'cod' === $payment_method ) {
				$cod_only = true;
			}
			$operators = $helper->getOperatorsForWidget(
				WC()->cart->get_cart_contents_total(),
				null,
				$cod_only
			);

			$operators = array_map(
				function ( $item ) {
					return array(
						'operator' => $item->operator,
						'price'    => $item->price->gross,
					);
				},
				json_decode( $operators )
			);
			// @codingStandardsIgnoreStart
			echo " <a id='myBtn' href='#bpWidget_wrapper' " .
				"onclick='Bliskapaczka.showMap(" .
					esc_html( json_encode($operators) ) .
					', "' .
					esc_html( $helper->getGoogleMapApiKey( $bliskapaczka->settings ) ) .
					'", ' .
					esc_html( ( 'test' === $helper->getApiMode( $bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'] ) ? 'true' : 'false' ) ) .
					',' .
					esc_html( json_encode($cod_only)) .
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
			if ( 'bliskapaczka' === $chosen_shipping_method ) {
				echo '<div id="bpWidget_aboutPoint" style="width: 100%; ' . ( ( ! isset( $pos_info ) ) ? ' display: none; ' : '' ) . '">';
				echo '<p>' . esc_html( __( 'Selected Point', 'bliskapaczka-shipping-method' ) ) . ': <span id="bpWidget_aboutPoint_posData">';
				if ( isset( $pos_info ) ) {
					// @codingStandardsIgnoreStart
					echo '</br>' . esc_html( $pos_info->operator ) . '</br>' .
						 ( ( $pos_info->description ) ? esc_html( $pos_info->description ) . '</br>' : '' ) .
						 esc_html( $pos_info->street ) . '</br>' .

						 ( ( $pos_info->postalCode ) ? esc_html( $pos_info->postalCode ) . ' ' : '' ) . esc_html( $pos_info->city );
					// @codingStandardsIgnoreEnd
				}
				echo '</span></p>';
				echo '</div>';
			}
		}
	}

	/**
	 * Add wrapper div for wigdet and map with parcel points.
	 *
	 * @param mixed $checkout Some data.
	 */
	function add_widget_div( $checkout ) {

		$class_modal  = 'class="modal"';
		$class_widget = 'class="modal-content"';
        // @codingStandardsIgnoreStart
        if ( false === strpos( $_SERVER['HTTP_HOST'], 'bliskapaczka' )) {
            $class_modal  = '';
            $class_widget = '';
        }

        echo '<div id="myModal" ' . $class_modal . '>';
        echo '<div id="myModal" >';
        echo '<div id="bpWidget_wrapper">';
        echo "<a name='bpWidget_wrapper'><a/>";
        echo '<div id="bpWidget" ' . $class_widget . '></div>';
        echo '<div id="bpWidget" ></div>';
        echo '</div>';
        echo '</div>';
        // @codingStandardsIgnoreEnd

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

		$checkout_data = [];

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
		$logger = new WC_Logger();
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
				$order_data = $mapper->prepareInsuranceDataIfNeeded( $order_data, $order );
			}
			try {
				$logger->info( wp_json_encode( $order_data ) );
				$api_client = $helper->getApiClientOrder( $bliskapaczka );
				$result     = $api_client->create( $order_data );
				if ( $helper->isAutoAdvice( $bliskapaczka ) === true ) {
					$advice_api_client = $helper->getApiClientTodoorAdvice( $bliskapaczka );
					$advice_api_client->setOrderId( json_decode( $result, true )['number'] );
					$advice_api_client->create( $order_data );
				}
				$order->update_meta_data( '_bliskapaczka_order_id', json_decode( $result, true )['number'] );
				$order->update_meta_data( '_need_to_pickup', true );
				$order->save();
			} catch ( Exception $e ) {
				$logger->error( $e->getMessage() );
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
				$order_data = $mapper->prepareInsuranceDataIfNeeded( $order_data, $order );
			}
			$api_client = $helper->getApiClientOrder( $bliskapaczka );
			$result     = $api_client->create( $order_data );
			if ( $helper->isAutoAdvice( $bliskapaczka ) === true ) {
				$advice_api_client = $helper->getApiClientOrderAdvice( $bliskapaczka );

				$advice_api_client->setOrderId( json_decode( $result, true )['number'] );
				$advice_api_client->create( $order_data );
			}
			$order->update_meta_data( '_bliskapaczka_order_id', json_decode( $result, true )['number'] );
			$order->update_meta_data( '_need_to_pickup', false );
			$order->save();
			WC()->session->set( 'bliskapaczka_posCode', '' );
			WC()->session->set( 'bliskapaczka_posOperator', '' );
		} catch ( Exception $e ) {
			$logger->error( $e->getMessage() );
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

		wp_register_script( 'widget-script', 'https://widget.bliskapaczka.pl/v5/main.js', array(), 'v5', false );
		wp_enqueue_script( 'widget-script' );

		wp_register_style( 'widget-styles', 'https://widget.bliskapaczka.pl/v5/main.css', array(), 'v5', false );
		wp_enqueue_style( 'widget-styles' );
		wp_register_style( 'widget-styles-bliskapaczka', plugin_dir_url( __FILE__ ) . 'assets/css/bliskapaczka.css', array(), 'v5', false );
		wp_enqueue_style( 'widget-styles-bliskapaczka' );

		wp_register_script( 'plugin-script', plugin_dir_url( __FILE__ ) . 'assets/js/bliskapaczka.js', array(), 'v5', false );
		wp_enqueue_script( 'plugin-script' );
		wp_localize_script(
			'plugin-script',
			'BliskapaczkaAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( Bliskapaczka_Shipping_Method_Helper::getAjaxNonce() ),
			)
		);
	}
    function bliskapaczka_admin_styles(){
        wp_register_style( 'bliskapaczka_admin_styles', plugin_dir_url( __FILE__ ) . 'assets/css/bliskapaczka_admin.css', array(), 'v1', false );
        wp_enqueue_style( 'bliskapaczka_admin_styles' );
    }
    add_action('admin_enqueue_scripts', 'bliskapaczka_admin_styles');

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'plugin_action_links' );

	add_filter( 'woocommerce_shipping_packages', 'update_price_for_chosen_carrier' );

	add_action( 'wp_enqueue_scripts', 'add_scripts_and_scripts' );

	add_filter( 'woocommerce_shipping_methods', 'add_bliskapaczka_shipping_method' );
	add_action( 'woocommerce_after_shipping_rate', 'show_map_anchorn' );
	add_action( 'woocommerce_after_checkout_form', 'add_widget_div' );

	add_action( 'woocommerce_checkout_update_order_meta', 'create_order_via_api' );

	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

	add_action( 'woocommerce_calculate_totals', 'set_shipping_cost', 10 );
	add_filter( 'woocommerce_order_button_html', 'disabled_checkout_button' );
	add_filter( 'woocommerce_add_error', 'my_woocommerce_add_error' );

	/**
	 * Display polish error.
	 *
	 * @param string $error Error.
	 *
	 * @return string
	 */
	function my_woocommerce_add_error( $error ) {
        // @codingStandardsIgnoreStart
		if ( false === strpos( $_SERVER['HTTP_HOST'], 'bliskapaczka' ) ) {
			return $error;
		}
        // @codingStandardsIgnoreEnd
		$logger = new WC_Logger();
		$logger->error( $error );
		return 'Wystąpił błąd w przetwarzaniu zamówienia. Jeśli błąd będzie się powtarzał,
		prosimy o kontakt.';
	}

	/**
	 * Disable button if needed
	 *
	 * @param string $button_html HTML.
	 *
	 * @return string
	 */
	function disabled_checkout_button( $button_html ) {

		$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];
		$pos_code               = WC()->session->get( 'bliskapaczka_posCode' );
		$pos_operator           = WC()->session->get( 'bliskapaczka_posOperator' );
		if ( 'bliskapaczka-courier' === $chosen_shipping_method && empty( $pos_operator ) ) {
			return str_replace(
				'<button',
				'<button disabled  style="color:#fff;cursor:not-allowed;background-color:#999;"',
				$button_html
			);
		}
		if ( 'bliskapaczka' === $chosen_shipping_method ) {
			if ( empty( $pos_code ) ) {
				return str_replace(
					'<button',
					'<button disabled  style="color:#fff;cursor:not-allowed;background-color:#999;"',
					$button_html
				);
			}
			if ( empty( $pos_operator ) ) {
				return str_replace(
					'<button',
					'<button disabled  style="color:#fff;cursor:not-allowed;background-color:#999;"',
					$button_html
				);
			}
			return $button_html;
		}
		return $button_html;
	}
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
		$pos_operator = $pos_code = null;

		if (isset($_POST['post_data'])) {
			$post_data = array();
			parse_str( $_POST['post_data'], $post_data );
			$pos_operator = $post_data['bliskapaczka_posOperator'];
			$pos_code     = $post_data['bliskapaczka_posCode'];
		}
		
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
			$price = $method->recalculate_shipping_cost(
				WC()->cart->get_cart_contents_total(),
				$pos_operator,
				$pos_code,
				$is_cod
			);

		}
		return $price;
	}

	/**
	 * Handle a custom 'customvar' query var to get orders with the 'customvar' meta.
	 *
	 * @param array $query - Args for WP_Query.
	 * @param array $query_vars - Query vars from WC_Order_Query.
	 * @return array modified $query
	 */
	function handle_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['_need_to_pickup'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_need_to_pickup',
				'value' => esc_attr( $query_vars['_need_to_pickup'] ),
			);
		}

		return $query;
	}
	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_var', 10, 2 );

	/**
	 * Handle a switch courier on the cart page.
	 *
	 * It's a hook for remember selected courier on a cart page.
	 */
	function bliskapaczka_wc_cart_switch_courier() {

		// Check nonce, will die if it's bad.
		check_ajax_referer( Bliskapaczka_Shipping_Method_Helper::getAjaxNonce(), 'security' );

		$req_key = 'bliskapaczka_posOperator';

		if ( isset( $_POST[ $req_key ] ) && ! empty( $_POST[ $req_key ] ) ) {
			$pos_operator = esc_html( sanitize_text_field( wp_unslash( $_POST[ $req_key ] ) ) );

			// @TODO verify its a courier allowed.
			WC()->session->set( 'bliskapaczka_posoperator', $pos_operator );

			WC()->cart->calculate_totals();
		}

		ob_start();
			wc_cart_totals_order_total_html();
			$content = ob_get_contents();
		ob_end_clean();

		echo wp_json_encode( array( 'order_total_html' => $content ) );
		wp_die();
	}

	add_action( 'wp_ajax_bliskapaczka_wc_cart_switch_courier', 'bliskapaczka_wc_cart_switch_courier' );
	add_action( 'wp_ajax_nopriv_bliskapaczka_wc_cart_switch_courier', 'bliskapaczka_wc_cart_switch_courier' );

}
