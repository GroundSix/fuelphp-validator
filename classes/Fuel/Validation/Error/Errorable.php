<?php
/**
 * Validation library
 *
 * @package    Fuel\Validation
 * @version    1.0.0
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 */

namespace Fuel\Validation\Error;

use Fuel\Validation\Value\Valuable as Value;

/**
 * Validation value interface
 *
 * @package  Fuel\Validation
 *
 * @since  1.0.0
 */
interface Errorable
{
	/**
	 * Constructor
	 *
	 * @param  \Fuel\Validation\Value\Valuable  $val
	 * @param  string                           $message
	 *
	 * @since  1.0.0
	 */
	public function __construct(Value $val, $message);

	/**
	 * Returns the error message
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getMessage();

	/**
	 * Returns the key that failed validation
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getKey();

	/**
	 * Returns the parent validation instance
	 *
	 * @return  \Fuel\Validation\Value\Valuable
	 *
	 * @since  1.0.0
	 */
	public function getValue();

	/**
	 * Returns a string representation of the error
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function __toString();
}
