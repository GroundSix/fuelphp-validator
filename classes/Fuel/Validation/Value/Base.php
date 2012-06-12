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
 * @since  1.0.0
 */
class Base implements Valuable
{
	/**
	 * @var  \Fuel\Validation\Base
	 */
	protected $validation;

	/**
	 * @var  string
	 */
	protected $key;

	/**
	 * @var  string
	 */
	protected $error;

	/**
	 * Constructs a value representation, takes the parent Validation, the
	 * input by reference and the key the value of which this is about.
	 *
	 * @param  \Fuel\Validation\Base  $val
	 * @param  string                 $key
	 *
	 * @since  1.0.0
	 */
	public function __construct(Validation\Base $val, $key)
	{
		$this->validation = $val;
		$this->key = $key;
	}

	/**
	 * Returns the value this is about by reference
	 *
	 * @return  mixed  creates and sets the value to null in the input when not set
	 *
	 * @since  1.0.0
	 */
	public function & get()
	{
		return $this->validation->getInput($this->key);
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
		$this->validation->setInput($this->key, $newValue);
		return $this;
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
