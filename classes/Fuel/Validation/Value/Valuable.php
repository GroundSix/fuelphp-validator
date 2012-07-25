<?php
/**
 * Validation library
 *
 * @package    Fuel\Validation
 * @version    1.0.0
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 */

namespace Fuel\Validation\Value;

use Fuel\Validation;

/**
 * Validation value interface
 *
 * @package  Fuel\Validation
 *
 * @method  require
 * @method  matchesValue
 * @method  matchesInput
 * @method  matchesPattern
 * @method  inArray
 * @method  atLeastChars
 * @method  atMostChars
 * @method  exactChars
 * @method  atLeastNum
 * @method  atMostNum
 * @method  isEmail
 * @method  isUrl
 * @method  isIp
 *
 * @since  1.0.0
 */
interface Valuable
{
	/**
	 * Constructs a value representation, takes the parent Validation, the
	 * input by reference and the key the value of which this is about.
	 *
	 * @param  \Fuel\Validation\Base  $val
	 * @param  string                 $key
	 *
	 * @since  1.0.0
	 */
	public function __construct(Validation\Base $val, $key);

	/**
	 * Returns the value this is about by reference
	 *
	 * @return  mixed  creates and sets the value to null in the input when not set
	 *
	 * @since  1.0.0
	 */
	public function get();

	/**
	 * Modifies the value this object represents
	 *
	 * @param   mixed  $newValue
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function set($newValue);

	/**
	 * Changes the key for value in the input
	 *
	 * @param   string  $key
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setKey($key);

	/**
	 * Returns the key for value in the input
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getKey();

	/**
	 * Changes the label for value
	 *
	 * @param   string  $label
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setLabel($label);

	/**
	 * Returns the label for value
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getLabel();

	/**
	 * A value to indicate the error
	 *
	 * @return  \Fuel\Validation\Error\Errorable|string
	 *
	 * @since  1.0.0
	 */
	public function getError();

	/**
	 * A value to indicate the error
	 *
	 * @param   string  $error
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setError($error);

	/**
	 * Unsets the error status
	 *
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function resetError();

	/**
	 * Check if this value validated
	 *
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validates();

	/**
	 * Returns the parent validation instance
	 *
	 * @return  \Fuel\Validation\Base
	 *
	 * @since  1.0.0
	 */
	public function getValidation();
}
