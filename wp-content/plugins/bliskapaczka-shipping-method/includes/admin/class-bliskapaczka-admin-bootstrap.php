<?php
/**
 * Bliskapaczka.pl Admin Bootstrap
 *
 * @author      Bliskapaczka.pl
 * @category    Admin
 * @package     Bliskapaczka/Admin/Order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *  Bliskapacza boot in admin
 */
class Bliskapaczka_Admin_Bootstrap {

	/**
	 * Instance of this class
	 *
	 * @var Bliskapaczka_Admin_Bootstrap
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	private function __construct() {    }

	/**
	 * Singleton
	 *
	 * @return Bliskapaczka_Admin_Bootstrap
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Bliskapaczka_Admin_Bootstrap();
		}

		return self::$instance;
	}

	/**
	 * Initialize all admin operations
	 *
	 * @return Bliskapaczka_Admin_Bootstrap
	 */
	public function boot() {
		if ( $this->is_admin() ) {
			$this
				->registry_actions()
				->registry_filters();
		}

		return $this;
	}

	/**
	 * Registers actions in administration
	 *
	 * @return Bliskapaczka_Admin_Bootstrap
	 */
	private function registry_actions() {
		add_filter( 'woocommerce_admin_order_data_after_shipping_address', [ new Bliskapaczka_Admin_Order_Details(), 'shipping_details' ], 1 );
		return $this;
	}

	/**
	 * Registers filters in administration
	 *
	 * @return Bliskapaczka_Admin_Bootstrap
	 */
	private function registry_filters() {
		return $this;
	}

	/**
	 * Return true if the request is from Admin Panel.
	 *
	 * @return boolean
	 */
	private function is_admin() {
		return is_admin();
	}
}
