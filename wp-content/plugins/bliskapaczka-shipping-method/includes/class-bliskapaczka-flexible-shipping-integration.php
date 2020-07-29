<?php
/**
 * Bliskapaczka Flexible Shippping Integration.
 *
 * @author      Bliskapaczka.pl
 * @category    Plugin
 * @package     Bliskapaczka
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bliskapaczka Flexible Shippping Integration.
 */
class Bliskapaczka_Flexible_Shipping_Integration {

	/**
	 * Flexible Shipping integration identity.
	 *
	 * @var string
	 */
	const INTEGRATION_NAME = 'bliskapaczka_fs';

	/**
	 * Delivery to point.
	 *
	 * @var string
	 */
	const OPTION_KEY_NAME_MAP = 'TO_POINT';

	/**
	 * Singleton instance.
	 *
	 * @var Bliskapaczka_Flexible_Shipping_Integration.
	 */
	private static $instance;

	/**
	 * List of shipping operators.
	 *
	 * @var array
	 */
	private static $operators;

	/**
	 * Private constructor, to get instance of this class please call.
	 * Bliskapaczka_​Flexible_Shipping_Integration::instance();
	 */
	private function __construct() {
		$this->initialize();
	}

	/**
	 * Returns a signle instance of this helper.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The method check if ​Flexible Shipping plugin is installed.
	 *
	 * @return boolean TRUE if the ​Flexible Shipping plugin is installend.
	 */
	public static function is_plugin_fs_installed() {
		static $installed;

		if ( ! isset( $installed ) ) {
			$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

			$installed = in_array( 'flexible-shipping/flexible-shipping.php', $active_plugins, true );
		}

		return $installed;
	}

	/**
	 * Return information if Flexible Shipping integretation is enabled.
	 *
	 * @return boolean TRUE only when Flexible Shipping integretation is enabled.
	 */
	public static function is_integration_enabled() {
		return Bliskapaczka_Shipping_Method_Helper::instance()->isFlexibleShippingIntegrationEnabled();
	}


	/**
	 * Append the integration name.
	 *
	 * @param array $options Integration options.
	 * @return \ArrayAccess|array Integration options.
	 */
	public function fs_integration_options( $options ) {
		if ( \is_array( $options ) || ( \is_object( $options ) && $options instanceof \ArrayAccess ) ) {
			$options[ self::INTEGRATION_NAME ] = __( 'Bliskapaczka', 'bliskapaczka-pl' );
		}

		return $options;
	}

	/**
	 * Show information about requiremets.
	 */
	public static function notice_plugin_fs_required() {
		// don't show message if the integrations is not enabled.
		if ( ! self::is_integration_enabled() ) {
			return;
		}

		if ( self::is_plugin_fs_installed() ) {
			return;
		}

		$slug         = 'flexible-shipping';
		$install_url  = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin=' . rawurlencode( 'flexible-shipping/flexible-shipping.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . rawurlencode( wp_create_nonce( 'activate-plugin_flexible-shipping/flexible-shipping.php' ) );

		/* translators: %s: Instalation url. */
		$msg_text = __( 'Flexible Shipping Bliskapaczka Integration requires Flexible Shipping Plugin. <a href="%s">Install Flexible Shipping →</a>', 'bliskapaczka-pl' );
		$message  = sprintf( wp_kses( $msg_text, array( 'a' => array( 'href' => array() ) ) ), esc_url( $install_url ) );

		$plugins = array_keys( get_plugins() );

		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'flexible-shipping/flexible-shipping.php' ) === 0 ) {
				/* translators: %s: Activation url. */
				$msg_text = __( 'Flexible Shipping Bliskapaczka Integration requires Flexible Shipping Plugin. <a href="%s">Activate Flexible Shipping →</a>', 'bliskapaczka-pl' );
				$message  = sprintf( wp_kses( $msg_text, array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( $activate_url ) ) );
				break;
			}
		}
		echo wp_kses_post( '<div class="notice notice-error error bliskapaczka-error"><p>' . $message . '</p></div>' . "\n" );
	}

	/**
	 * Append integretion settings in Flexibile Shipping "add method" form.
	 *
	 * The method is called via 'flexible_shipping_method_settings' hook.
	 *
	 * @param array $flexible_shipping_settings Shipping settings.
	 * @param  array $shipping_method Shipping method.
	 * @return array
	 */
	public function fs_method_settings( $flexible_shipping_settings, $shipping_method ) {

		$operator_setting = $this->get_name_of_operator_setting();

		$settings = array(
			$operator_setting => array(
				'title'   => __( 'Operator', 'bliskapaczka-pl' ),
				'type'    => 'select',
				'default' => isset( $shipping_method[ $operator_setting ] ) ? $shipping_method[ $operator_setting ] : '',
				'options' => self::$operators,
			),
		);
		return array_merge( $flexible_shipping_settings, $settings );
	}

	/**
	 * JavaScript for settings in integration.
	 */
	public function fs_method_script() {
		$operator_id_selector = '#woocommerce_flexible_shipping_' . $this->get_name_of_operator_setting();

		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					function bliskapaczka_fs_options() {
						if ( jQuery('#woocommerce_flexible_shipping_method_integration').val() == '<?php echo esc_html( self::INTEGRATION_NAME ); ?>' ) {
							jQuery('<?php echo esc_html( $operator_id_selector ); ?>').closest('tr').css('display','table-row');
						}
						else {
							jQuery('<?php echo esc_html( $operator_id_selector ); ?>').closest('tr').css('display','none');
						}
					}
					jQuery('#woocommerce_flexible_shipping_method_integration').change(function() {
						bliskapaczka_fs_options();
					});
					bliskapaczka_fs_options();
				});
			</script>
		<?php
	}

	/**
	 * Process the data in adminstation.
	 *
	 * The method is called via 'flexible_shipping_process_admin_options' hook.
	 *
	 * @param array $shipping_method Shipping method.
	 * @return array
	 */
	public function fs_process_admin_options( $shipping_method ) {

		if ( self::is_integration_enabled() ) {
			$req_key = 'woocommerce_flexible_shipping_' . $this->get_name_of_operator_setting();

			// @codingStandardsIgnoreStart
			if ( isset( $_POST[ $req_key ] ) ) {
				$shipping_method[ $this->get_name_of_operator_setting() ] = esc_html( sanitize_text_field( wp_unslash( $_POST[ $req_key ] ) ) );
			}
			// @codingStandardsIgnoreEnd
		}
		return $shipping_method;
	}


	/**
	 * Prints the informations in the integration colum in the shipping zone panel.
	 *
	 * @param string $col The body od the col.
	 * @param array  $shipping_method Shipping method data.
	 * @return mixed
	 */
	public function fs_method_integration_col( $col, $shipping_method ) {
		if ( isset( $shipping_method['method_integration'] ) && self::INTEGRATION_NAME === $shipping_method['method_integration'] ) {
			$operator = isset( $shipping_method[ $this->get_name_of_operator_setting() ] ) ? $shipping_method[ $this->get_name_of_operator_setting() ] : '';
			$tip      = trim( 'BliskaPaczka ' . $operator );
			ob_start();
			?>
			<td width="1%" class="integration default">
				<span class="tips" data-tip="<?php echo esc_html( $tip ); ?>">
					BliskaPaczka
				</span>
			</td>
			<?php
			$col = ob_get_contents();
			ob_end_clean();
		}
		return $col;
	}


	/**
	 *
	 * Called by 'flexible_shipping_method_rate_id' hook.
	 *
	 * @param string $rate_id WooCommerce shipping rate.
	 * @param array  $shipping_method Shipping method data.
	 * @return string
	 */
	public function fs_method_rate_id( $rate_id, $shipping_method ) {
		if ( isset( $shipping_method['method_integration'] ) && self::INTEGRATION_NAME === $shipping_method['method_integration'] ) {
			$rate_id = $rate_id . '_' . self::INTEGRATION_NAME . '_' . sanitize_title( $shipping_method[ $this->get_name_of_operator_setting() ] );
		}
		return $rate_id;
	}

	/**
	 * Check additional conditions ie. package contents and return false if this method is not avaliable.
	 *
	 * Called by 'flexible_shipping_add_method' hook.
	 *
	 * @param array $add_method Method to add.
	 * @param array $shipping_method  Shipping method.
	 * @param array $package Package.
	 * @return array
	 */
	public function fs_add_method( $add_method, $shipping_method, $package ) {
		// Guard  showing widget only once.
		static $widget_showed;

		if ( isset( $shipping_method['method_integration'] )
			&& self::INTEGRATION_NAME === $shipping_method['method_integration']
			&& isset( $shipping_method[ $this->get_name_of_operator_setting() ] )
			) {

			if ( ! self::is_integration_enabled() ) {
				return false;
			}

			$operator = $shipping_method[ $this->get_name_of_operator_setting() ];

			// For map widget, we don't availability verify.
			if ( self::OPTION_KEY_NAME_MAP === $operator ) {

				if ( isset( $widget_showed ) ) {
					return false;
				}

				$widget_showed = true;
				return $add_method;
			}

			$helper = Bliskapaczka_Shipping_Method_Helper::instance();
			$total  = WC()->cart->get_cart_contents_total();
			$is_cod = $helper->isChoosedPaymentCOD();

			$prices = $helper->getCourierShippingMethod()->get_price_list(
				$total,
				$is_cod
			);

			foreach ( $prices as $item ) {
				if ( $item->operator() === $operator ) {
					return $add_method;
				}
			}

			// Courier are not avaible for this settings.
			$msg = sprintf( 'Bliskapaczka operator "%s" has missed configuration for paramaters: (price: %s, COD: %s). Please verify settings in BliskaPaczka panel.', $operator, $total, $is_cod ? 'yes' : 'no' );
			wc_get_logger()->warning( $msg );

			return false;
		}
		return $add_method;
	}

	/**
	 * Initialize integration with ​Flexible Shipping plugin only when it is installed and enabled.
	 */
	private function initialize() {

			static::$operators = [

				'DHL'                     => __( 'DHL', 'bliskapaczka-pl' ),
				'DPD'                     => __( 'DPD', 'bliskapaczka-pl' ),
				'FEDEX'                   => __( 'FedEx', 'bliskapaczka-pl' ),
				'GLS'                     => __( 'GLS', 'bliskapaczka-pl' ),
				'INPOST'                  => __( 'Inpost', 'bliskapaczka-pl' ),
				'POCZTA'                  => __( 'Poczta Polska', 'bliskapaczka-pl' ),
				'UPS'                     => __( 'UPS', 'bliskapaczka-pl' ),
				'XPRESS'                  => __( 'X-press', 'bliskapaczka-pl' ),
				self::OPTION_KEY_NAME_MAP => __( 'Paczkomaty / Punkty', 'bliskapaczka-pl' ),
			];
			$this->register_hooks();

	}

	/**
	 * Return the name of the setting when the choosed opeartor are stored.
	 *
	 * @return string
	 */
	public static function get_name_of_operator_setting() {
		return self::INTEGRATION_NAME . '_operator';
	}

	/**
	 * Render widget link and form fields for the front checkout view.
	 *
	 * @param WC_Shipping_Rate $method WooCommerce Shipping Rate.
	 * @param string           $index Index.
	 */
	public function wc_after_shipping_rate( WC_Shipping_Rate $method, $index ) {

		if ( 'flexible_shipping' === $method->method_id ) {
			$meta = $method->get_meta_data();

			if ( ! isset( $meta['_fs_method'] ) ) {
				return;
			}
			$meta_fs = $meta['_fs_method'];

			if ( is_checkout()
				&& isset( $meta_fs['method_integration'] )
				&& self::INTEGRATION_NAME === $meta_fs['method_integration']
				&& isset( $meta_fs[ $this->get_name_of_operator_setting() ] )
				&& self::OPTION_KEY_NAME_MAP === $meta_fs[ $this->get_name_of_operator_setting() ]
			) {

				bliskapaczka_delivery_to_point_print_widget_in_checkout_view( false );

				$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];

				if ( $method->id === $chosen_shipping_method ) {
					bliskapaczka_delivery_to_point_print_choosed_point_address( false );
				}
			}
		}
	}

	/**
	 * Send order to bliskapaczka.
	 *
	 * @param string $order_id Woocommerce order id.
	 */
	public function send_order_to_api( $order_id ) {
		if ( ! self::is_integration_enabled() ) {
			return;
		}

		if ( 0 < $order_id ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return;
		}

		$helper             = Bliskapaczka_Shipping_Method_Helper::instance();
		$shipping_method_id = $helper->getWCShipingMethodId( $order );

		if ( 'flexible_shipping' !== $shipping_method_id ) {
			return;
		}

		$shippings        = $order->get_shipping_methods();
		$shipping_item_id = array_keys( $shippings )[0];
		$shipping_item    = $shippings[ $shipping_item_id ];

		$fs_meta = $shipping_item->get_meta( '_fs_method' );

		// if its not shipping.
		if ( ! isset( $fs_meta['method_integration'] ) || self::INTEGRATION_NAME !== $fs_meta['method_integration'] || ! isset( $fs_meta [ $this->get_name_of_operator_setting() ] ) ) {
			return;
		}

		// get the operator.
		$fs_operator = $fs_meta[ $this->get_name_of_operator_setting() ];

		// operator from map widget.
		if ( self::OPTION_KEY_NAME_MAP === $fs_operator ) {
			$delivery_to_point = true;
			$operator          = WC()->session->get( 'bliskapaczka_point_operator' );
			$pos_code          = WC()->session->get( 'bliskapaczka_point_code' );
			// operator from.
		} else {
			$delivery_to_point = false;
			$operator          = $fs_operator;
		}

		$helper->remember_shipping_item_data( $shipping_item_id, $operator, $pos_code );
		$helper->send_order_to_api( $order, $delivery_to_point );
	}

	/**
	 * Registering the admin hooks.
	 */
	private function register_hooks() {

		// IMPORTANT 1. This hooks work always when plugin is installed.
		// admin configuration.
		add_filter( 'flexible_shipping_integration_options', array( $this, 'fs_integration_options' ), 10 );
		add_filter( 'flexible_shipping_method_settings', array( $this, 'fs_method_settings' ), 10, 2 );
		add_filter( 'flexible_shipping_process_admin_options', array( $this, 'fs_process_admin_options' ), 10, 1 );
		add_filter( 'flexible_shipping_method_integration_col', array( $this, 'fs_method_integration_col' ), 10, 2 );

		add_action( 'flexible_shipping_method_script', array( $this, 'fs_method_script' ) );

		// checkout.
		add_filter( 'flexible_shipping_method_rate_id', array( $this, 'fs_method_rate_id' ), 10, 2 );
		add_filter( 'flexible_shipping_add_method', array( $this, 'fs_add_method' ), 10, 3 );

		// IMPORTANT 2. This hooks work only when intgeration is enabled.
		if ( self::is_integration_enabled() ) {
			// admin.
			add_action( 'admin_notices', array( $this, 'notice_plugin_fs_required' ) );

			// checkout.
			add_action( 'woocommerce_after_shipping_rate', array( $this, 'wc_after_shipping_rate' ), 10, 2 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'send_order_to_api' ), 10, 1 );
		}
	}

}
