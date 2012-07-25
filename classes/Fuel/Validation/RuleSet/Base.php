<?php
/**
 * Validation library
 *
 * @package    Fuel\Validation
 * @version    1.0.0
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 */

namespace Fuel\Validation\RuleSet;

use Fuel\Validation\Value\Valuable as Value;

/**
 * A base set of rules
 *
 * @package  Fuel\Validation
 *
 * @since  1.0.0
 */
class Base
{
	/**
	 * Validates whether something is an empty value: null, '' or array()
	 *
	 * @param   mixed  $var
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	protected function _empty($var)
	{
		return $var === null or $var === '' or $var === array();
	}

	/**
	 * Validates whether a given value is empty (int 0, boolean false and '0' don't count as empty)
	 *
	 * @param   Value  $v
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateRequire(Value $v)
	{
		if ($this->_empty($v->get()))
		{
			$v->setError('nonEmpty');
			return false;
		}

		return true;
	}

	/**
	 * Checks if the Value matches a given one
	 *
	 * @param   Value  $v
	 * @param   mixed  $against
	 * @param   bool   $strict
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateMatchesValue(Value $v, $against, $strict = false)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		// match...
		if ($strict ? $v->get() !== $against : $v->get() != $against)
		{
			$v->setError('matchesValue');
			return false;
		}

		return true;
	}

	/**
	 * Checks if the given Value matches another key in the input
	 *
	 * @param   Value   $v
	 * @param   string  $key
	 * @param   bool    $strict
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateMatchesInput(Value $v, $key, $strict = false)
	{
		$match = $this->validateMatchesValue($v, $v->getValidation()->getValue($key), $strict);
		! $match and $v->setError('matchesInput');

		return $match;
	}

	/**
	 * Checks if the given Value matches a given regex pattern
	 *
	 * @param   Value   $v
	 * @param   string  $pattern
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateMatchesPattern(Value $v, $pattern)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		// match...
		if (preg_match($pattern, $v->get()) > 0)
		{
			$v->setError('matchesPattern');
			return false;
		}

		return true;
	}

	/**
	 * Checks if the Value matches a given one
	 *
	 * @param   Value  $v
	 * @param   array  $array
	 * @param   bool   $strict
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateInArray(Value $v, array $array, $strict = false)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if (in_array($v->get(), $array, $strict))
		{
			$v->setError('inArray');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the field has at least a specific length
	 *
	 * @param   Value  $v
	 * @param   int    $length
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateAtLeastChars(Value $v, $length)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ((function_exists('mb_strlen') ? mb_strlen($v->get()) : strlen($v->get())) < $length)
		{
			$v->setError('atLeastChars');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the field has at most a specific length
	 *
	 * @param   Value  $v
	 * @param   int    $length
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateAtMostChars(Value $v, $length)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ((function_exists('mb_strlen') ? mb_strlen($v->get()) : strlen($v->get())) > $length)
		{
			$v->setError('atMostChars');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the field has exactly a specific length
	 *
	 * @param   Value  $v
	 * @param   int    $length
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateExactChars(Value $v, $length)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ((function_exists('mb_strlen') ? mb_strlen($v->get()) : strlen($v->get())) != $length)
		{
			$v->setError('atExactChars');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the field has at least a specific length
	 *
	 * @param   Value  $v
	 * @param   int    $number
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateAtLeastNum(Value $v, $number)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ($v->get() < $number)
		{
			$v->setError('atLeastNum');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the field has at most a specific length
	 *
	 * @param   Value  $v
	 * @param   int    $number
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateAtMostNum(Value $v, $number)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ($v->get() > $number)
		{
			$v->setError('atMostNum');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the given emailaddress passes PHP's filter_var()
	 *
	 * @param   Value   $v
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateIsEmail(Value $v)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ( ! filter_var($v->get(), FILTER_VALIDATE_EMAIL))
		{
			$v->setError('isEmail');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the given URL passes PHP's filter_var()
	 *
	 * @param   Value   $v
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateIsUrl(Value $v)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ( ! filter_var($v->get(), FILTER_VALIDATE_URL))
		{
			$v->setError('isUrl');
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the given IP passes PHP's filter_var()
	 *
	 * @param   Value   $v
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function validateIsIp(Value $v)
	{
		// matching only done when non-empty
		if ($this->_empty($v->get()))
		{
			return true;
		}

		if ( ! filter_var($v->get(), FILTER_VALIDATE_IP))
		{
			$v->setError('isIp');
			return false;
		}

		return true;
	}
}
