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
 * Validation value base implementation
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
class Base implements Valuable
{
	/**
	 * @var  \Fuel\Validation\Base
	 *
	 * @since  1.0.0
	 */
	protected $validation;

	/**
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $key;

	/**
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $label;

	/**
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $error;

	/**
	 * Constructs a value representation, takes the parent Validation, the
	 * input by reference and the key the value of which this is about.
	 *
	 * @param  \Fuel\Validation\Base  $val
	 * @param  string                 $key
	 * @param  string|null            $label
	 *
	 * @since  1.0.0
	 */
	public function __construct(Validation\Base $val, $key, $label = null)
	{
		$this->validation = $val;
		$this->key = $key;
		$this->label = $label;
	}

	/**
	 * Returns the value this is about by reference
	 *
	 * @return  mixed  creates and sets the value to null in the input when not set
	 *
	 * @since  1.0.0
	 */
	public function get()
	{
		return $this->validation->getValue($this->key);
	}

	/**
	 * Modifies the value this object represents
	 *
	 * @param   mixed  $newValue
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function set($newValue)
	{
		$this->validation->setValue($this->key, $newValue);
		return $this;
	}

	/**
	 * Returns the key of this value in the input
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Changes the input key for the value
	 *
	 * @param   string  $key
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setKey($key)
	{
		$this->key = $key;
		return $this;
	}

	/**
	 * Returns the label for this value in the input
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Changes the label for the value
	 *
	 * @param   string  $label
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	/**
	 * A value to indicate the error
	 *
	 * @return  \Fuel\Validation\Error\Errorable|string
	 *
	 * @since  1.0.0
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * A value to indicate the error
	 *
	 * @param   string  $error
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function setError($error)
	{
		$this->error = $error;
		return $this;
	}

	/**
	 * Unsets the error status
	 *
	 * @return  Valuable
	 *
	 * @since  1.0.0
	 */
	public function resetError()
	{
		$this->error = null;
		return $this;
	}

	/**
	 * Check if this value validated
	 *
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validates()
	{
		return is_null($this->error);
	}

	/**
	 * Returns the parent validation instance
	 *
	 * @return  \Fuel\Validation\Base
	 *
	 * @since  1.0.0
	 */
	public function getValidation()
	{
		return $this->validation;
	}

	/**
	 * Supports calling validation methods on this object
	 *
	 * @param   string  $method
	 * @param   array   $args
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function __call($method, array $args)
	{
		return $this->validation->executeRule($method, $this, $args);
	}
}
