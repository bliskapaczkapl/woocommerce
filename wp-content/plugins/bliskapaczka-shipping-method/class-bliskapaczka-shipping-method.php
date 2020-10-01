<?php
/**
 * Plugin Name: Bliskapaczka.pl
 * Plugin URI: https://bliskapaczka.pl/narzedzia/integracja-sklep-wordpress-woocommerce
 * Description: Integracja metod dostaw z serwisem Bliskapaczka.pl
 * Version: 1.4.1
 * Author: Bliskapaczka.pl
 * Text Domain: bliskapaczka-pl
 * Domain Path: /languages
 *
 * @package  Bliskapaczka
 * @subpackage Woocommerce
 * @author Bliskapaczka
 * @copyright Bliskapaczka
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
load_plugin_textdomain( 'bliskapaczka-pl', false, basename( dirname( __FILE__ ) ) . '/languages' );
define( 'BLISKAPACZKA_ABSPATH', dirname( __FILE__ ) . '/' );
require_once 'includes/class-bliskapaczka-loader.php';

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	include_once __DIR__ . '/../woocommerce/woocommerce.php';

	/**
	 * Bliskapaczka Shipping Method
	 */
	function bliskapaczka_map_shipping_method() {
		require_once 'includes/class-bliskapaczka-map-shipping-method.php';
	}

	add_action( 'woocommerce_shipping_init', 'bliskapaczka_map_shipping_method' );

	/**
	 * Bliskapaczka Courier Shipping Method
	 */
	function bliskapaczka_courier_shipping_method() {
		require_once 'includes/class-bliskapaczka-courier-shipping-method.php';
	}

	add_action( 'woocommerce_shipping_init', 'bliskapaczka_courier_shipping_method' );

	/**
	 * Add Bliskapaczka courier shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function bliskapaczka_delivery_to_door_shipping_method_registry( $methods ) {

		$helper = Bliskapaczka_Shipping_Method_Helper::instance();

		return $helper->append_to_wc_methods( $helper->getCourierShippingMethod(), $methods );
	}

	add_filter( 'woocommerce_shipping_methods', 'bliskapaczka_delivery_to_door_shipping_method_registry' );

	/**
	 * Add Bliskapaczka shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function bliskapaczka_delivery_to_point_shipping_method_registry( $methods ) {

		$helper = Bliskapaczka_Shipping_Method_Helper::instance();

		return $helper->append_to_wc_methods( $helper->getMapShippingMethod(), $methods );
	}

	add_filter( 'woocommerce_shipping_methods', 'bliskapaczka_delivery_to_point_shipping_method_registry' );

	/**
	 * Add link for select parcel point displayed on checkout page.
	 *
	 * @param Bliskapaczka_Courier_Shipping_Method $method Bliskapaczka Courier Shipping method.
	 */
	function bliskapaczka_delivery_to_door_front_view( $method ) {

		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() !== $method->id ) {
			return;
		}

		$helper       = Bliskapaczka_Shipping_Method_Helper::instance();
		$cod_only     = $helper->isChoosedPaymentCOD();
		$bliskapaczka = $helper->getCourierShippingMethod();

		$price_list = $bliskapaczka->get_price_list(
			WC()->cart->get_cart_contents_total(),
			$cod_only
		);

		// If we don't have a price list, that we don't show the method.
		if ( $price_list->count() === 0 ) {
			wc_get_logger()->warning( 'No door to door courier selected in bliskpacza.pl' );
			return;
		}

		// Selected opertor detection.
		$operator_found    = false;
		$selected_operator = WC()->session->get( 'bliskapaczka_door_operator', null );

		// Verify if the operator exists.
		if ( null !== $selected_operator ) {
			foreach ( $price_list as $item ) {
				if ( $item->operator() === $selected_operator ) {
					$operator_found = true;
					break;
				}
			}
		}

		// If none selected take the first one and remeber it.
		if ( false === $operator_found ) {
			$selected_operator = $price_list[0]->operator();
			WC()->session->set( 'bliskapaczka_door_operator', $selected_operator );
		}

		// Generete the view.
		$dir_img = plugin_dir_url( __FILE__ ) . 'assets/img/operators/';

		echo '<div class="bliskapaczka_courier_wrapper">';
		foreach ( $price_list as $item ) {

			$css_selected_class = $item->operator() === $selected_operator ? 'checked' : '';

			echo '<label class="bliskapaczka_courier_item_wrapper ' . esc_html( $css_selected_class ) . '" for="bliskapaczka_door_operator" data-operator="' . esc_html( $item->operator() ) . '">';
			echo '<input type="radio" name="bliskapaczka_door_operator" value="' . esc_html( $item->operator() ) . '">';
			echo '<div class="bliskapaczka_courier_item">';
			echo '<div class="bliskapaczka_courier_item_logo"><img src="' . esc_html( $dir_img . $item->operator() ) . '.png" alt="' . esc_html( $item->operator() ) . '" style="height: 25px; width: auto"></div>';
			echo '<div class="bliskapaczka_courier_item_price">';
			echo '<span class="bliskapaczka_courier_item_price_value">' . esc_html( $item->gross() ) . '</span><span>zł</span>';
			echo '</div>';
			echo '</div>';
			echo '</label>';
		}

	}

	add_action( 'woocommerce_after_shipping_rate', 'bliskapaczka_delivery_to_door_front_view' );

	/**
	 * Add link for select parcel point displayed on checkout page.
	 *
	 * @param Bliskapaczka_Map_Shipping_Method $method Bliskapaczka Shipping method.
	 */
	function bliskapaczka_delivery_to_point_front_view( $method ) {

		if ( Bliskapaczka_Map_Shipping_Method::get_identity() === $method->id && is_checkout() === true ) {

			bliskapaczka_delivery_to_point_print_widget_in_checkout_view( true );

			$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];

			if ( Bliskapaczka_Map_Shipping_Method::get_identity() === $chosen_shipping_method ) {
				bliskapaczka_delivery_to_point_print_choosed_point_address( true );
			}
		}
	}

	add_action( 'woocommerce_after_shipping_rate', 'bliskapaczka_delivery_to_point_front_view' );

	/**
	 * Render widget link and form fields for the front checkout view.
	 *
	 * @param boolean $with_price Set to true if you whant show prices.
	 * @return string
	 */
	function bliskapaczka_delivery_to_point_print_widget_in_checkout_view( $with_price = true ) {
		$helper       = Bliskapaczka_Shipping_Method_Helper::instance();
		$bliskapaczka = $helper->getMapShippingMethod();

		$cod_only     = $helper->isChoosedPaymentCOD();
		$pos_code     = WC()->session->get( 'bliskapaczka_point_code' );
		$pos_operator = WC()->session->get( 'bliskapaczka_point_operator', null );

		$price_list = $bliskapaczka->get_price_list( WC()->cart->get_cart_contents_total(), $cod_only );
		if ( count( $price_list ) === 0 ) {
			return '';
		}

		$operators = [];
		$idx       = 0;

		foreach ( $price_list as $item ) {
			$operators[ $idx ] = [
				'operator' => $item->operator(),
			];

			if ( true === $with_price ) {
				$operators[ $idx ]['price'] = $item->gross();
			}
			$idx++;
		}

		// @codingStandardsIgnoreStart
		echo " <a id='bliskapaczka-delivery-to-point-btn' href='#bpWidget_wrapper' " .
			"onclick='Bliskapaczka.showMap(" .
			esc_html( json_encode($operators) ) .
			', "' .
			esc_html( $helper->getGoogleMapApiKey( $bliskapaczka->settings ) ) .
			'", ' .
			esc_html( ( 'test' === $helper->getApiMode() ? 'true' : 'false' ) ) .
			',' .
			esc_html( json_encode($cod_only)) .
			")'>" .
			esc_html( 'Wybierz punkt dostawy' ) . '</a>';
			// @codingStandardsIgnoreEnd
			echo '<input name="bliskapaczka_point_code" type="hidden" id="bliskapaczka-point-code" value="' . esc_html( $pos_code ) . '" />';
			echo '<input name="bliskapaczka_point_operator" type="hidden" id="bliskapaczka-point-operator" value="' . esc_html( $pos_operator ) . '" />';

	}

	/**
	 * Show the address of selected point via widget
	 *
	 * @param boolean $with_price Set to true if you whant show prices.
	 */
	function bliskapaczka_delivery_to_point_print_choosed_point_address( $with_price = true ) {
		$pos_code     = WC()->session->get( 'bliskapaczka_point_code' );
		$pos_operator = WC()->session->get( 'bliskapaczka_point_operator', null );
		$helper       = Bliskapaczka_Shipping_Method_Helper::instance();

		if ( $pos_code && $pos_operator ) {
			$pos_info = $helper->getPosInfo( $pos_operator, $pos_code );
		}

		echo '<div id="bpWidget_aboutPoint" style="width: 100%; ' . ( ( ! isset( $pos_info ) ) ? ' display: none; ' : '' ) . '">';
		echo '<p>' . esc_html( __( 'Selected Point', 'bliskapaczka-pl' ) ) . ': <span id="bpWidget_aboutPoint_posData">';
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

		if ( true === $with_price ) {
			echo '<div><strong>' . esc_html( __( 'Shipping cost', 'bliskapaczka-pl' ) ) . ': ' . wp_kses_post( wc_price( bliskapaczka_get_price() ) ) . '</strong></div>';
		}

	}

	/**
	 * Add wrapper div for wigdet and map with parcel points.
	 *
	 * @param mixed $checkout Some data.
	 */
	function bliskapaczka_add_widget_div( $checkout ) {

		echo '<div id="bliskapaczka-modal">';
		echo '	<div id="bpWidget_wrapper">';
		echo '		<a name="bpWidget_wrapper"><a/>';
		echo '		<div id="bpWidget"></div>';
		echo '	</div>';
		echo '</div>';
	}

	add_action( 'woocommerce_after_checkout_form', 'bliskapaczka_add_widget_div' );

	/**
	 * Add fields to manage POS code and POS operator to checkout
	 *
	 * @param array $fields Checkput fields list.
	 */
	function bliskapaczka_override_checkout_fields( $fields ) {
		$fields['bliskapaczka'] = array(
			'bliskapaczka_point_code'     => array(
				'label' => __( 'POS Code', 'bliskapaczka-pl' ),
				'type'  => 'text',
			),
			'bliskapaczka_point_operator' => array(
				'label' => __( 'POS Operator', 'bliskapaczka-pl' ),
				'type'  => 'text',
			),
		);
		return $fields;
	}

	add_filter( 'woocommerce_checkout_fields', 'bliskapaczka_override_checkout_fields' );

	/**
	 * For shipping method choosen by customer we should display valid price on cart page.
	 *
	 * @param mixed $packages Some data.
	 */
	function bliskapaczka_update_price_for_chosen_carrier( $packages ) {

		$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];

		if ( Bliskapaczka_Map_Shipping_Method::get_identity() !== $chosen_shipping_method
			&& 0 !== strpos( $chosen_shipping_method, 'flexible_shipping_' )
		) {
			return $packages;
		}

		$checkout_data = [];

		// @codingStandardsIgnoreStart
	 	if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $checkout_data );
		} else {
			return $packages;
		}
		// @codingStandardsIgnoreEnd

		if ( ! isset( $checkout_data['bliskapaczka_point_code'] ) || ! isset( $checkout_data['bliskapaczka_point_code'] ) ) {
			return $packages;
		}

		$pos_code     = wc_clean( $checkout_data['bliskapaczka_point_code'] );
		$pos_operator = wc_clean( $checkout_data['bliskapaczka_point_operator'] );

		if ( null === $pos_operator || mb_strlen( $pos_operator ) < 3 ) {
			WC()->session->__unset( 'bliskapaczka_point_operator' );
			WC()->session->__unset( 'bliskapaczka_point_code' );
		} else {
			WC()->session->set( 'bliskapaczka_point_code', $pos_code );
			WC()->session->set( 'bliskapaczka_point_operator', $pos_operator );
		}

		return $packages;
	}

	add_filter( 'woocommerce_shipping_packages', 'bliskapaczka_update_price_for_chosen_carrier' );

	/**
	 * Create new order in Bliska Paczka if shipping method is bliskapaczka.
	 *
	 * @param int $order_id Order ID.
	 * @throws Exception If can't send data to bliskapaczka.
	 */
	function bliskapaczka_create_order_via_api( $order_id ) {

		if ( 0 < $order_id ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return false;
		}

		$helper             = Bliskapaczka_Shipping_Method_Helper::instance();
		$pos_code           = null;
		$shipping_method_id = $helper->getWCShipingMethodId( $order );

		// If shipping is not from bliskpaczka. We do nothing.
		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() !== $shipping_method_id && Bliskapaczka_Map_Shipping_Method::get_identity() !== $shipping_method_id ) {
			return;
		}

		// Update the price of shipping item.
		$shipping_items     = $order->get_items( array( 'shipping' ) );
		$shipping_items_ids = array_keys( $shipping_items );

		$shipping_item_id = $shipping_items_ids[0];
		$shipping_item    = $shipping_items[ $shipping_item_id ];

		if ( $shipping_item instanceof \WC_Order_Item_Shipping ) {
			$shipping_item->set_total( bliskapaczka_get_price() );
		}

		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() === $shipping_method_id ) {
			$delivery_to_point = false;
			$operator          = WC()->session->get( 'bliskapaczka_door_operator' );
		} elseif ( Bliskapaczka_Map_Shipping_Method::get_identity() === $shipping_method_id ) {
			$operator          = WC()->session->get( 'bliskapaczka_point_operator' );
			$pos_code          = WC()->session->get( 'bliskapaczka_point_code' );
			$delivery_to_point = true;
		} else {
			return;
		}

		$helper->remember_shipping_item_data( $shipping_item_id, $operator, $pos_code );
		$helper->send_order_to_api( $order, $delivery_to_point );
	}

	/**
	 * Add link to settings on plugins list in admin panel.
	 *
	 * @param array $links Links list.
	 */
	function bliskapaczka_plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=bliskapaczka' ) .
				'" title="' . esc_attr( __( 'View Bliskapaczka Settings', 'bliskapaczka-pl' ) ) . '">' . __( 'Settings', 'bliskapaczka' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Include JS.
	 */
	function bliskapaczka_add_scripts_and_scripts() {

		wp_register_script( 'widget-script', 'https://widget.bliskapaczka.pl/v5.15/main.js', array(), 'v5.15', false );
		wp_enqueue_script( 'widget-script' );

		wp_register_style( 'widget-styles', 'https://widget.bliskapaczka.pl/v5.15/main.css', array(), 'v5.15', false );
		wp_enqueue_style( 'widget-styles' );
		wp_register_style( 'widget-styles-bliskapaczka', plugin_dir_url( __FILE__ ) . 'assets/css/bliskapaczka.css', array(), 'v8', false );
		wp_enqueue_style( 'widget-styles-bliskapaczka' );

		wp_register_script( 'plugin-script', plugin_dir_url( __FILE__ ) . 'assets/js/bliskapaczka.js', array(), 'v10', false );
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

	/**
	 * Custom checkout validation.
	 * Plugin uses default Woocommerce validation for e-mail, postal code and phone with some clean-up functions to meet API requirements.
	 *
	 * @param array $fields List of fiels.
	 * @param array $errors List of errors.
	 */
	function bliskapaczka_checkout_validation( $fields, $errors ) {

		$bliskapaczka_method_checker = WC()->session->get( 'chosen_shipping_methods' )[0];
		if ( Bliskapaczka_Map_Shipping_Method::get_identity() === $bliskapaczka_method_checker || Bliskapaczka_Courier_Shipping_Method::get_identity() === $bliskapaczka_method_checker ) {

			if ( strlen( $fields['billing_first_name'] ) > 30 || strlen( $fields['shipping_first_name'] ) > 30 ) {
				$errors->add( 'validation', esc_html__( 'First name is longer than 30 characters.', 'bliskapaczka-pl' ) );
			}
			if ( strlen( $fields['billing_last_name'] ) > 30 || strlen( $fields['shipping_last_name'] ) > 30 ) {
				$errors->add( 'validation', esc_html__( 'Last name is logner than 30 characters.', 'bliskapaczka-pl' ) );
			}
			if ( strlen( $fields['billing_city'] ) > 30 || strlen( $fields['shipping_city'] ) > 30 ) {
				$errors->add( 'validation', esc_html__( 'City name cannot exceed 30 characters.', 'bliskapaczka-pl' ) );
			}
		}
	}
	add_action( 'woocommerce_after_checkout_validation', 'bliskapaczka_checkout_validation', 10, 2 );

	/**
	 * Include custom CSS.
	 */
	function bliskapaczka_admin_styles() {
		wp_register_style( 'bliskapaczka_admin_styles', plugin_dir_url( __FILE__ ) . 'assets/css/bliskapaczka_admin.css', array(), 'v1', false );
		wp_enqueue_style( 'bliskapaczka_admin_styles' );
	}
	add_action( 'admin_enqueue_scripts', 'bliskapaczka_admin_styles' );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bliskapaczka_plugin_action_links' );

	add_action( 'wp_enqueue_scripts', 'bliskapaczka_add_scripts_and_scripts' );

	add_action( 'woocommerce_checkout_update_order_meta', 'bliskapaczka_create_order_via_api' );

	add_action( 'woocommerce_calculate_totals', 'bliskapaczka_set_shipping_cost', 10 );
	add_filter( 'woocommerce_order_button_html', 'bliskapaczka_disabled_checkout_button' );

	/**
	 * Disable button if needed
	 *
	 * @param string $button_html HTML.
	 *
	 * @return string
	 */
	function bliskapaczka_disabled_checkout_button( $button_html ) {

		$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];
		$pos_code               = WC()->session->get( 'bliskapaczka_point_code' );
		$door_operator          = WC()->session->get( 'bliskapaczka_door_operator' );
		$point_operator         = WC()->session->get( 'bliskapaczka_point_operator' );
		$disabled               = false;

		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() === $chosen_shipping_method && empty( $door_operator ) ) {
			$disabled = true;
		} elseif ( Bliskapaczka_Map_Shipping_Method::get_identity() === $chosen_shipping_method ) {
			if ( empty( $pos_code ) ) {
				$disabled = true;
			} elseif ( empty( $point_operator ) ) {
				$disabled = true;
			}
		}

		if ( $disabled ) {
			return str_replace(
				'<button',
				'<button disabled  style="color:#fff;cursor:not-allowed;background-color:#999;"',
				$button_html
			);
		}

		return $button_html;
	}
	/**
	 * Set correct price
	 *
	 * @param mixed $cart Cart from Woocommerce.
	 */
	function bliskapaczka_set_shipping_cost( $cart ) {
		$price          = bliskapaczka_get_price();
		$shipping_total = $cart->get_shipping_total();
		$cart->set_shipping_total( $shipping_total + $price );
	}

	add_filter( 'woocommerce_calculated_total', 'bliskapaczka_calculated_total', 10, 2 );

	/**
	 * Recalculate total price
	 *
	 * @param float $total Total price.
	 * @param mixed $cart Object from Woocommerce.
	 *
	 * @return int
	 */
	function bliskapaczka_calculated_total( $total, $cart ) {

		return $total + bliskapaczka_get_price();
	}

	/**
	 * Return price
	 *
	 * @return int
	 */
	function bliskapaczka_get_price() {
		$helper = Bliskapaczka_Shipping_Method_Helper::instance();

		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_method  = $chosen_methods[0];
		$cart_total     = WC()->cart->get_cart_contents_total();
		$is_cod         = $helper->isChoosedPaymentCOD();

		switch ( $chosen_method ) {
			// to door.
			case Bliskapaczka_Courier_Shipping_Method::get_identity():
				$operator     = WC()->session->get( 'bliskapaczka_door_operator', null );
				$bliskapaczka = $helper->getCourierShippingMethod();
				break;
			// to point.
			case Bliskapaczka_Map_Shipping_Method::get_identity():
				$operator     = WC()->session->get( 'bliskapaczka_point_operator', null );
				$bliskapaczka = $helper->getMapShippingMethod();
				break;
		}

		if ( isset( $bliskapaczka ) && isset( $operator ) ) {

			if ( null === $operator || mb_strlen( $operator ) < 3 ) {
				return 0; // The operator was not choosed, so we return 0 as price.
			}

			return $bliskapaczka->get_price(
				$cart_total,
				$is_cod,
				$operator
			);
		}

		return 0;
	}

	/**
	 * Handle a custom 'customvar' query var to get orders with the 'customvar' meta.
	 *
	 * @param array $query - Args for WP_Query.
	 * @param array $query_vars - Query vars from WC_Order_Query.
	 * @return array modified $query
	 */
	function bliskapaczka_handle_custom_query_var( $query, $query_vars ) {
		if ( Bliskapaczka_Shipping_Method_Helper::FUNCTIONALITY_AUTO_ADVICE_ENABLED === true && ! empty( $query_vars['_bliskapaczka_need_to_pickup'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_bliskapaczka_need_to_pickup',
				'value' => esc_attr( $query_vars['_bliskapaczka_need_to_pickup'] ),
			);
		}

		return $query;
	}
	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'bliskapaczka_handle_custom_query_var', 10, 2 );

	/**
	 * Handle a switch courier on the cart page.
	 *
	 * It's a hook for remember selected courier on a cart page.
	 */
	function bliskapaczka_delivery_to_door_switch_courier() {

		// Check nonce, will die if it's bad.
		check_ajax_referer( Bliskapaczka_Shipping_Method_Helper::getAjaxNonce(), 'security' );

		$req_key = 'bliskapaczka_door_operator';

		if ( isset( $_POST[ $req_key ] ) && ! empty( $_POST[ $req_key ] ) ) {
			$operator = esc_html( sanitize_text_field( wp_unslash( $_POST[ $req_key ] ) ) );

			// @TODO verify its a courier allowed.
			WC()->session->set( 'bliskapaczka_door_operator', $operator );

			WC()->cart->calculate_totals();
		}

		ob_start();
			wc_cart_totals_order_total_html();
			$content = ob_get_contents();
		ob_end_clean();

		echo wp_json_encode( array( 'order_total_html' => $content ) );
		wp_die();
	}

	add_action( 'wp_ajax_bliskapaczka_delivery_to_door_switch_courier', 'bliskapaczka_delivery_to_door_switch_courier' );
	add_action( 'wp_ajax_nopriv_bliskapaczka_delivery_to_door_switch_courier', 'bliskapaczka_delivery_to_door_switch_courier' );

	/**
	 * This method is called always after checkout are submitted and before is proceeded
	 *
	 * @param string $data The value from 'post_data' index of $_POST.
	 */
	function bliskapaczka_checkout_update_order_review( $data ) {
		check_ajax_referer( 'update-order-review', 'security' );

		$post_data = [];
		parse_str( $data, $post_data );

		$helper     = Bliskapaczka_Shipping_Method_Helper::instance();
		$cart_total = WC()->cart->get_cart_contents_total();

		// Get shipping method.
		if ( isset( $post_data['shipping_method'] ) && is_array( $post_data['shipping_method'] ) ) {
			$chosen_method = esc_html( sanitize_text_field( wp_unslash( $post_data['shipping_method'][0] ) ) );
		} else {
			$chosen_method = WC()->session->get( 'chosen_shipping_methods' )[0];
		}

		// Get payment method.
		if ( isset( $post_data['payment_method'] ) ) {
			$is_cod = 'cod' === esc_html( sanitize_text_field( wp_unslash( $post_data['payment_method'] ) ) );
		} else {
			$is_cod = $helper->isChoosedPaymentCOD();
		}

		if ( Bliskapaczka_Map_Shipping_Method::get_identity() === $chosen_method
			|| 0 === strpos( $chosen_method, 'flexible_shipping_' )
		) {
			$bliskapaczka   = $helper->getMapShippingMethod();
			$operator_index = 'bliskapaczka_point_operator';
		} elseif ( Bliskapaczka_Courier_Shipping_Method::get_identity() === $chosen_method ) {
			$bliskapaczka   = $helper->getCourierShippingMethod();
			$operator_index = 'bliskapaczka_door_operator';
		} else {
			return;
		}
				$operator = WC()->session->get( $operator_index );
		$price_list       = $bliskapaczka->get_price_list( $cart_total, $is_cod );

		// Verfify that the choosen operator exists.
		foreach ( $price_list as $item ) {
			if ( $item->operator() === $operator ) {
				return; // if exists, we are good, and do nothing.
			}
		}

		// We don't found the choosen operator so we must switch.
		WC()->session->set( $operator_index, $price_list[0]->operator() );

		// We are cleaning the choosen delivery.
		WC()->session->__unset( 'bliskapaczka_point_code' );
	}

	add_action( 'woocommerce_checkout_update_order_review', 'bliskapaczka_checkout_update_order_review' );

	/**
	 * Replacing default Shipping information on thank you page.
	 *
	 * @param array $order Order data.
	 * @param int   $order_id Order id.
	 */
	function bliskapaczka_thank_you_page_display_shipped_via( $order, $order_id ) {
		$helper = Bliskapaczka_Shipping_Method_Helper::instance();
		$order  = wc_get_order( $order_id );
		foreach ( array_keys( $order->get_items( array( 'shipping' ) ) ) as $item_id ) {
			$shipping_item_id = $item_id;
		}
		$operator           = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator' );
		$shipping_method_id = $helper->getWCShipingMethodId( $order );
		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() === $shipping_method_id ) {
			/* translators: %s: method */
			return '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . ' ' . $operator . '</small>';
		}
		if ( Bliskapaczka_Map_Shipping_Method::get_identity() === $shipping_method_id ) {
			/* translators: %s: method */
			return '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . ' ' . $operator . '</small>';
		}
	}

	add_filter( 'woocommerce_order_shipping_to_display_shipped_via', 'bliskapaczka_thank_you_page_display_shipped_via', 10, 2 );

	Bliskapaczka_Flexible_Shipping_Integration::instance();

	// Registry operation in admin panel.
	if ( is_admin() ) {
		Bliskapaczka_Admin_Bootstrap::instance()->boot();
	}
}
/**
 * Detailed info about point to point delivery
 *
 * @param array $formatted_meta Formatted order metadata.
 */
function bliskapaczka_order_meta_data_view( $formatted_meta ) {
	foreach ( $formatted_meta as $obj ) {
		if ( '_bliskapaczka_posCode' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Point code', 'bliskapaczka-pl' ) );
		}
		if ( '_bliskapaczka_posOperator' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Operator', 'bliskapaczka-pl' ) );
		}
		if ( '_bliskapaczka_posInfo' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Point info', 'bliskapaczka-pl' ) );
		}
	}

	return $formatted_meta;
}
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'bliskapaczka_order_meta_data_view', 10, 2 );
