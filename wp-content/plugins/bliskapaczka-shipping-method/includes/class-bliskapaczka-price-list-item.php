<?php
/**
 * Bliskapaczka.pl WooCommerce plugin
 */
if (! defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

/**
 * Bliskapaczka price list item
 *
 * @author Bliskapaczka.pl
 * @package Bliskapaczka
 */
class Bliskapaczka_Price_List_Item
{

	/**
	 * Operator name
	 *
	 * @var string
	 */
	private $operator;

	/**
	 * Price net
	 *
	 * @var float
	 */
	private $net;

	/**
	 * Tax
	 *
	 * @var float
	 */
	private $tax;

	/**
	 * Price gross
	 *
	 * @var float
	 */
	private $gross;

	/**
	 * Constructor
	 *
	 * @param string $operator
	 *        	Operator name
	 * @param float $net
	 *        	Price net
	 * @param float $tax
	 *        	Tax
	 * @param float $gross
	 *        	Price gross
	 */
	public function __construct($operator, $net, $tax, $gross)
	{
		$this->operator = $operator;
		$this->net = $net;
		$this->tax = $tax;
		$this->gross = $gross;
	}

	/**
	 * Returns the operator name.
	 * Ex UPS, DPD.
	 *
	 * @return string Operator name
	 */
	public function operator()
	{
		return $this->operator;
	}

	/**
	 * Rreturns the price without tax
	 *
	 * @return float
	 */
	public function net()
	{
		return $this->net;
	}

	/**
	 * Returns the tax value.
	 *
	 * @return float
	 */
	public function tax()
	{
		return $this->tax;
	}

	/**
	 * Returns the price with tax.
	 *
	 * @return float
	 */
	public function gross()
	{
		return $this->gross;
	}
}