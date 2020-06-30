<?php
/**
 * Bliskapaczka.pl WooCommerce plugin
 */
if (! defined('ABSPATH')) {
	exit();
}

/**
 * Base class for all shipping methods from bliskapczka.
 * 
 * @author      Bliskapaczka.pl
 * @category    Admin
 * @package     Bliskapaczka
 */
abstract class Bliskapaczka_Shipping_Method_Base extends WC_Shipping_Method
{

	/**
	 * @var Bliskapaczka_Shipping_Method_Helper
	 */
	private $helper;

	/**
	 * Returns the delivery method identity
	 * 
	 * @return string Delivery method identity
	 */
	abstract public static function get_identity();
	
	/**
	 * Returns the price list for the shipping method.
	 *
	 * @param float $cart_total
	 *        	The total price for order.$this
	 * @param boolean $is_cod
	 *        	TRUE if the payment method is cash on delivery.
	 *        	
	 * @return Bliskapaczka_Price_List
	 */
	abstract public function get_price_list($cart_total, $is_cod = false);

	/**
	 * Get the price for shipping
	 *
	 * @param float $cart_total
	 *        	Total value of cart
	 * @param boolean $is_cod
	 *        	TRUE if it is cash on delivery
	 * @param float $operator
	 *        	Operator short name ( like: UPS, DPD)
	 * @throws \Bliskapaczka_Exception
	 * @return float
	 */
	public function get_price($cart_total, $is_cod = false, $operator)
	{
		$price_list = $this->get_price_list($cart_total, $is_cod);

		foreach ($price_list as $item) {
			if ($item->operator() === $operator) {
				return $item->gross();
			}
		}

		// If we are here, that this mean that the given operator not found.
		throw new \Bliskapaczka_Exception('The given operator was not found in current offer.');
	}

	/**
	 * Returns instance of the bliskapaczka helper.
	 *
	 * @return Bliskapaczka_Shipping_Method_Helper
	 */
	protected function helper()
	{
		if (! isset($this->helper)) {
			$this->helper = Bliskapaczka_Shipping_Method_Helper::instance();
		}
		return $this->helper;
	}
}
