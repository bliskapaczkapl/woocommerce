<?php
/**
 * Bliskapaczka.pl WooCommerce plugin
 */
if (! defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

/**
 * Bliskapaczka price list
 *
 * @author Bliskapaczka.pl
 * @package Bliskapaczka
 */
class Bliskapaczka_Price_List extends ArrayObject
{

	/**
	 * Constructor
	 *
	 * @see ArrayObject::__construct()
	 *
	 * @param array $input
	 *        	The input parameter accepts an array or an Object.
	 * @param number $flags
	 *        	Flags to control the behaviour of the ArrayObject object. See ArrayObject::setFlags().
	 * @param string $iterator_class
	 *        	Specify the class that will be used for iteration of the ArrayObject object.
	 *        	
	 * @throws Bliskapaczka_Exception If the value is not allowed
	 */
	public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
	{
		if (count($input) > 0) {
			foreach ($input as $e) {
				$this->isAllowed($e);
			}
		}
		parent::__construct($input, $flags, $iterator_class);
	}

	/**
	 * Overwited metod, to verify that the Bliskapaczka_Price_List_Item is added
	 *
	 * @param Bliskapaczka_Price_List_Item $value
	 *        	Element to append
	 *        	
	 * @see ArrayObject::append()
	 */
	public function append($value)
	{
		$this->isAllowed($value);

		parent::append($value);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see ArrayObject::offsetSet()
	 */
	public function offsetSet($index, $newval)
	{
		$this->isAllowed($newval);

		return parent::offsetSet($index, $newval);
	}

	/**
	 * Verfiy that the given value, can be element of the collection.
	 *
	 * If not that the exception is throw
	 *
	 * @param mixed $value
	 *        	Value to check
	 * @throws Bliskapaczka_Exception If the value is not allowed
	 *        
	 * @return Bliskapaczka_Price_List
	 */
	private function isAllowed($value)
	{
		if (! ($value instanceof Bliskapaczka_Price_List_Item)) {
			throw new Bliskapaczka_Exception('Only Bliskapaczka_Price_List_Item type allowed');
		}

		return $this;
	}
}

