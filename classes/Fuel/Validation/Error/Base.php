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
 * Validation value base implementation
 *
 * @package  Fuel\Validation
 *
 * @since  1.0.0
 */
class Base implements Errorable
{
	/**
	 * @var  Value
	 *
	 * @since  1.0.0
	 */
	protected $value;

	/**
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $message;

	/**
	 * Constructor
	 *
	 * @param  \Fuel\Validation\Value\Valuable  $val
	 * @param  string                           $message
	 *
	 * @since  1.0.0
	 */
	public function __construct(Value $val, $message)
	{
		$this->value    = $val;
		$this->message  = $message;
	}

	/**
	 * Returns the error message
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getMessage()
	{
		$message = $this->value->getValidation()->getMessage($this->message, $this->message);

		$replacements = array(
			'{name}'   => $this->getKey(),
			'{value}'  => $this->getValue()->get(),
			'{label}'  => $this->getValue()->getLabel(),
		);

		return str_replace(array_keys($replacements), array_values($replacements), $message);
	}

	/**
	 * Returns the key that failed validation
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getKey()
	{
		return $this->value->getKey();
	}

	/**
	 * Returns the parent validation instance
	 *
	 * @return  \Fuel\Validation\Value\Valuable
	 *
	 * @since  1.0.0
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns a string representation of the error
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function __toString()
	{
		return $this->getMessage();
	}
}
