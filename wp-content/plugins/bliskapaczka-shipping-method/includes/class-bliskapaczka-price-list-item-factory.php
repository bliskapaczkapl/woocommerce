<?php
/**
 * Bliskapaczka.pl WooCommerce plugin
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bliskapaczka price list item factory 
 *
 * @author      Bliskapaczka.pl
 * @package     Bliskapaczka
 */
class Bliskapaczka_Price_List_Item_Factory
{
	/**
	 * Create price list item from item API response.
	 * 
	 * @param \stdClass $item Item from API 
	 *
	 * @return Bliskapaczka_Price_List_Item
	 */
	public static function fromApiItem($item) 
	{
		if ( ! ( $item instanceof \stdClass  ) ) {
			throw new \InvalidArgumentException('Excepted only \stdClass instance for $item attributte');
		}
		
		return new Bliskapaczka_Price_List_Item(
			$item->operatorName,
			$item->price->net,
			$item->price->vat,
			$item->price->gross
		);
	}
}
