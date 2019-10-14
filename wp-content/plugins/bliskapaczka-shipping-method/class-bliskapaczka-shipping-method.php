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
 * @author Mateusz Koszutowski
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
	function bliskapaczka_shipping_method() {
		if ( ! class_exists( 'Bliskapaczka_Shipping_Method' ) ) {
			/**
			 * Bliskapaczka Shipping Method
			 */
			class Bliskapaczka_Shipping_Method extends WC_Shipping_Method {
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
						$helper::TITLE                         => array(
							'title'       => __( 'Title', 'bliskapaczka-shipping-method' ),
							'type'        => 'text',
							'description' => __( 'Title to be display on site', 'bliskapaczka-shipping-method' ),
							'default'     => __( 'Bliskapaczka Shipping', 'bliskapaczka-shipping-method' ),
						),
						$helper::API_KEY                => array(
							'title'       => __( 'API Key', 'bliskapaczka-shipping-method' ),
							'type'        => 'text',
							'description' => __( 'API Key', 'bliskapaczka-shipping-method' ),
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
                    $bliskapaczka = new Bliskapaczka_Shipping_Method();
					$price_list     = $helper->getPriceList();
					$shipping_price = round( $helper->getLowestPrice( $price_list, true ), 2 );

					$rate = array(
						'id'       => $this->id,
						'label'    => $bliskapaczka->settings[$helper::TITLE],
						'cost'     => $shipping_price,
						'calc_tax' => 'per_item',
					);

					$this->add_rate( $rate );

				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'bliskapaczka_shipping_method' );

	/**
	 * Add Bliskapaczka shipping method to available methods.
	 *
	 * @param array $methods List of shipping methods.
	 */
	function add_bliskapaczka_shipping_method( $methods ) {
		$methods[] = 'Bliskapaczka_Shipping_Method';
		return $methods;
	}

	/**
	 * Add link for select parcel point displayed on checkout page.
	 *
	 * @param Bliskapaczka_Shipping_Method $method Bliskapaczka Shipping method.
	 */
	function show_map_anchorn( $method ) {
		$helper = new Bliskapaczka_Shipping_Method_Helper();

		$bliskapaczka = new Bliskapaczka_Shipping_Method();

		if ( 'bliskapaczka' === $method->id && is_checkout() === true ) {
			echo " <a href='#bpWidget_wrapper' " .
				"onclick='Bliskapaczka.showMap(" .
					esc_html( $helper->getOperatorsForWidget() ) .
					', "' .
					esc_html( $helper->getGoogleMapApiKey( $bliskapaczka->settings ) ) .
					'", ' .
					esc_html( ( 'test' === $helper->getApiMode( $bliskapaczka->settings['BLISKAPACZKA_TEST_MODE'] ) ? 'true' : 'false' ) ) .
					")'>" .
					esc_html( __( 'Select delivery point', 'bliskapaczka-shipping-method' ) ) . '</a>';
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
		echo '<div id="bpWidget" style="height: 600px; width: 800px; display: none;"></div>';
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

		if ( $pos_code && $pos_operator ) {
			WC()->session->set( 'bliskapaczka_posCode', $pos_code );
			WC()->session->set( 'bliskapaczka_posOperator', $pos_operator );

			$helper         = new Bliskapaczka_Shipping_Method_Helper();
			$price_list     = $helper->getPriceList();
			$shipping_price = round( $helper->getPriceForCarrier( $price_list, $pos_operator, true ), 2 );

			$packages[0]['rates']['bliskapaczka']->label = 'Bliskapaczka';
			$packages[0]['rates']['bliskapaczka']->cost  = $shipping_price;
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

        $bliskapaczka = new Bliskapaczka_Shipping_Method();
        $helper       = new Bliskapaczka_Shipping_Method_Helper();
		// TODO: "Bliskapaczka, od" to const.
        if ($order->get_shipping_method() !== 'Bliskapaczka' && $order->get_shipping_method() !== $bliskapaczka->settings[$helper::TITLE]) {
            return false;
        }


		$mapper       = new Bliskapaczka_Shipping_Method_Mapper();

		$order_data = $mapper->getData( $order, $helper, $bliskapaczka->settings );

		try {
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
		wp_register_script( 'widget-script', 'https://widget.bliskapaczka.pl/v5/main.js', array(), 'v5', false );
		wp_enqueue_script( 'widget-script' );

		wp_register_style( 'widget-styles', 'https://widget.bliskapaczka.pl/v5/main.css', array(), 'v5', false );
		wp_enqueue_style( 'widget-styles' );

		wp_register_script( 'plugin-script', plugin_dir_url( __FILE__ ) . 'assets/js/bliskapaczka.js', array(), 'v5', false );
		wp_enqueue_script( 'plugin-script' );
	}


	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'plugin_action_links' );

	add_filter( 'woocommerce_shipping_packages', 'update_price_for_chosen_carrier' );

	add_action( 'wp_enqueue_scripts', 'add_scripts_and_scripts' );

	add_filter( 'woocommerce_shipping_methods', 'add_bliskapaczka_shipping_method' );
	add_action( 'woocommerce_after_shipping_rate', 'show_map_anchorn' );
	add_action( 'woocommerce_after_checkout_form', 'add_widget_div' );

	add_action( 'woocommerce_checkout_update_order_meta', 'create_order_via_api' );

	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );
}
