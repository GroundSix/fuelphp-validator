<?php
/**
 * Validation library
 *
 * @package    Fuel\Validation
 * @version    1.0.0
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 */

namespace Fuel\Validation;

use ArrayAccess;
use Closure;

/**
 * Base Validation class
 *
 * @package  Fuel\Validation
 *
 * @since  1.0.0
 */
class Base
{
	/**
	 * @var  array  configuration
	 *
	 * @since  1.0.0
	 */
	protected $config = array(
		'valueClass'  => 'Fuel\\Validation\\Value\\Base',
		'errorClass'  => 'Fuel\\Validation\\Error\\Base',
	);

	/**
	 * @var  array  classes and objects to search for rules
	 *
	 * @since  1.0.0
	 */
	protected $ruleSets = array();

	/**
	 * @var  array  validators indexed by key
	 *
	 * @since  1.0.0
	 */
	protected $validators = array();

	/**
	 * @var  array|object  reference to the input
	 *
	 * @since  1.0.0
	 */
	protected $values;

	/**
	 * @var  array  just the values that validated
	 *
	 * @since  1.0.0
	 */
	protected $validated;

	/**
	 * @var  array  Error\Errorable
	 *
	 * @since  1.0.0
	 */
	protected $errors;

	/**
	 * @var  array  Validation error messages
	 */
	protected $messages;

	/**
	 * Constructor
	 *
	 * @param  array  $config
	 *
	 * @since  1.0.0
	 */
	public function __construct(array $config = array())
	{
		$this->config = $config + $this->config;
		$this->ruleSets[] = new RuleSet\Base();
	}

	/**
	 * Add a validator for a key
	 *
	 * @param   Value\Valuable|string  $value
	 * @param   Closure                $validator
	 * @param   string|null            $label
	 * @return  Base
	 *
	 * @since  1.0.0
	 */
	public function validate($value, Closure $validator, $label = null)
	{
		if ( ! $value instanceof Value\Valuable)
		{
			$class = $this->config['valueClass'];
			$value = new $class($this, $value, $label ?: $value);
		}

		$this->validators[$value->getKey()] = array($validator, $value);
		return $this;
	}

	/**
	 * Executes the validation
	 *
	 * @param   array|object  $values
	 * @param   array         $partial
	 * @return  bool
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function execute($values, array $partial = null)
	{
		$this->values     = $values;
		$this->validated  = array();
		$this->errors     = array();

		$validators = is_null($partial) ? array_keys($this->validators) : $partial;

		// Iterate over the validators
		foreach ($validators as $val)
		{
			if ( ! isset($this->validators[$val]))
			{
				throw new \RuntimeException('Unknown validator in partial validation.');
			}

			list($validator, $value) = $this->validators[$val];
			$values = $this->explodeKey($val, $value);

			foreach ($values as $v)
			{
				$this->executeValidator($validator, $v);
			}
		}

		return empty($this->errors);
	}

	/**
	 * Returns an value key by reference
	 *
	 * @param   null|string  $key
	 * @param   mixed        $default
	 * @return  mixed
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function getValue($key = null, $default = null)
	{
		if (is_null($this->values))
		{
			throw new \RuntimeException('Validation needs to run before input is available.');
		}

		// No args? Return the whole thing
		if (func_num_args() === 0)
		{
			return $this->values;
		}

		try
		{
			return $this->_arrayGet($key, $this->values);
		}
		catch (\OutOfBoundsException $e)
		{
			return $default;
		}
	}

	/**
	 * Modify a specific key in the values, will create arrays when necessary
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  Base
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function setValue($key, $value)
	{
		if (is_null($this->values))
		{
			throw new \RuntimeException('Validation needs to run before input is available.');
		}

		$this->values = $this->_arraySet($key, $this->values, $value);

		return $this;
	}

	/**
	 * Returns an validated value
	 *
	 * @param   null|string  $key
	 * @param   mixed        $default
	 * @return  mixed
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function getValidated($key = null, $default = null)
	{
		if (is_null($this->validated))
		{
			throw new \RuntimeException('Validation needs to run before validated values are available.');
		}

		// No args? Return the whole thing
		if (func_num_args() === 0)
		{
			return $this->validated;
		}

		try
		{
			return $this->_arrayGet($key, $this->validated);
		}
		catch (\OutOfBoundsException $e)
		{
			return $default;
		}
	}

	/**
	 * Modify a specific key in the validated values, will create arrays when necessary
	 *
	 * @param   \Fuel\Validation\Value\Valuable  $value
	 * @return  Base
	 *
	 * @since  1.0.0
	 */
	protected function _setValidated(Value\Valuable $value)
	{
		$this->validated = $this->_arraySet($value->getKey(), $this->validated, $value->get());
		return $this;
	}

	/**
	 * Returns an input key by reference
	 *
	 * @param   null|string  $key
	 * @return  Error\Errorable|bool
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function getError($key = null)
	{
		if (is_null($this->errors))
		{
			throw new \RuntimeException('Validation needs to run before errors are available.');
		}

		// No args? Return the whole thing
		if (func_num_args() === 0)
		{
			return $this->errors;
		}

		if ( ! array_key_exists($key, $this->errors))
		{
			return false;
		}

		return $this->errors[$key];
	}

	/**
	 * Get the error message for a key (or the full array of messages)
	 *
	 * @param   null|string  $key
	 * @return  array|string
	 *
	 * @return  1.0.0
	 */
	public function getErrorMessage($key = null)
	{
		if (func_num_args() === 0)
		{
			$error = $this->getError();
			$errors = array();
			foreach ($error as $key => $e)
			{
				$errors[$key] = strval($e);
			}

			return $errors;
		}

		return strval($this->getError($key));
	}

	/**
	 * Add a Ruleset to search for validation rules
	 *
	 * @param   string|object  $class  class name or object
	 * @param   null|string    $name
	 * @return  Base
	 *
	 * @since  1.0.0
	 */
	public function addRuleSet($class, $name = null)
	{
		is_null($name) and $name = uniqid();
		$this->ruleSets[$name] = $class;

		return $this;
	}

	/**
	 * Remove a named Ruleset from the Validation
	 * (unnamed RuleSets cannot be deleted unless you know their key)
	 *
	 * @param   string    $name
	 * @return  Base
	 *
	 * @since  1.0.0
	 */
	public function removeRuleSet($name)
	{
		unset($this->ruleSets[$name]);
		return $this;
	}

	/**
	 * Set a message for a given error key
	 *
	 * @param   string  $error
	 * @param   string  $message
	 * @return  Base
	 *
	 * @since  2.0.0
	 */
	public function setMessage($error, $message)
	{
		$this->messages[$error] = $message;
		return $this;
	}

	/**
	 * Get an error message for an error key
	 *
	 * @param   string  $error
	 * @param   mixed   $default
	 * @return  string
	 *
	 * @since  2.0.0
	 */
	public function getMessage($error, $default = null)
	{
		return isset($this->messages[$error]) ? $this->messages[$error] : $default;
	}

	/**
	 * Explode key with * to multiple keys with $value objects
	 *
	 * @param   string          $key
	 * @param   Value\Valuable  $value
	 * @return  array
	 *
	 * @since  1.0.0
	 */
	protected function explodeKey($key, Value\Valuable $value)
	{
		if (($pos = strpos($key, '*')) === false)
		{
			return array($value);
		}

		$keys       = array();
		$keyPrefix  = substr($key, 0, $pos);
		$keySuffix  = substr($key, $pos + 1);

		$values = $this->_arrayGet(rtrim($keyPrefix, '.'), $this->values);
		foreach ($values as $key => $val)
		{
			$k = $keyPrefix.$key.$keySuffix;
			if (strpos($keySuffix, '*') === false)
			{
				$v = clone $value;
				$keys[$k] = $v->setKey($k);
			}
			else
			{
				$keys += $this->explodeKey($k, $value);
			}
		}

		return $keys;
	}

	/**
	 * Executes the validator for the given value
	 *
	 * @param   Closure         $validator
	 * @param   Value\Valuable  $value
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function executeValidator(Closure $validator, Value\Valuable $value)
	{
		$validator($value);

		if ( ! $value->validates())
		{
			// Fetch the error and ensure it's wrapped in an Error object
			$error = $value->getError();
			if ( ! $error instanceof Error\Errorable)
			{
				$class = $this->config['errorClass'];
				$error = new $class($value, $error);
			}

			$this->errors[$value->getKey()] = $error;
		}
		else
		{
			$this->_setValidated($value);
		}
	}

	/**
	 * Attempts to executes a rule
	 *
	 * @param   string                           $rule
	 * @param   \Fuel\Validation\Value\Valuable  $value
	 * @param   array                            $args
	 * @return  bool
	 * @throws  \RuntimeException  when the rule was invalid
	 *
	 * @since  1.0.0
	 */
	public function executeRule($rule, Value\Valuable $value, array $args)
	{
		// Search rule sets for rules, method name must be prefixed 'validate'
		$r = strncmp($rule, 'validate', 8) === 0 ? $rule : 'validate'.ucfirst($rule);
		foreach ($this->ruleSets as $ruleSet)
		{
			if (method_exists($ruleSet, $r))
			{
				array_unshift($args, $value);
				return call_user_func_array(array($ruleSet, $r), $args);
			}
		}

		// No callable found? Attempt functions
		if (is_string($rule) and function_exists($rule))
		{
			array_unshift($args, $value->get());
			$return = call_user_func_array($rule, $args);

			// When the function returned boolean use it as a did/did-not validate
			if (is_bool($return))
			{
				! $return and $value->setError('function.'.$rule);
				return $return;
			}

			// When the PHP method returned non-boolean use it as a changed value
			$value->set($return);
			return true;
		}

		// All is lost, give up and throw an exception
		throw new \RuntimeException('The rule '.$rule.' was not found and could not be executed.');
	}

	/**
	 * Fetch a dot-notated value from an array or object
	 *
	 * @param   string        $key
	 * @param   array|object  $input
	 * @return  mixed
	 * @throws  \OutOfBoundsException
	 *
	 * @since  1.0.0
	 */
	protected function _arrayGet($key, $input)
	{
		// Just return it if there's only a single key (left)
		if (strpos($key, '.') === false)
		{
			if (is_object($input) and isset($input->{$key}))
			{
				return $input->{$key};
			}
			elseif ((is_array($input) or $input instanceof ArrayAccess) and isset($input[$key]))
			{
				return $input[$key];
			}

			// still here? key doesn't exist
			throw new \OutOfBoundsException($key);
		}

		// ... else get the next key and recurse
		$keys = explode('.', $key);
		$key = array_shift($keys);
		if (is_object($input))
		{
			! property_exists($input, $key) and $input->{$key} = array();
			return $this->_arrayGet(implode('.', $keys), $input[$key]);
		}
		elseif (is_array($input) or $input instanceof ArrayAccess)
		{
			! isset($input[$key]) and $input[$key] = array();
			return $this->_arrayGet(implode('.', $keys), $input[$key]);
		}

		// still here? key doesn't exist
		throw new \OutOfBoundsException($key);
	}

	/**
	 * Set a dot-notated value on an array or object
	 *
	 * @param   string        $key
	 * @param   array|object  $input
	 * @param   mixed         $value
	 * @return  array|object
	 *
	 * @since  1.0.0
	 */
	protected function _arraySet($key, $input, $value)
	{
		// Just change it if there's only a single key (left)
		if (strpos($key, '.') === false)
		{
			is_object($input) ? $input->{$key} = $value : $input[$key] = $value;
			return $input;
		}

		// ... else get the next key and recurse
		$keys = explode('.', $key);
		$key = array_shift($keys);
		if (is_array($input) or $input instanceof ArrayAccess)
		{
			! isset($input[$key]) and $input[$key] = array();
			$input[$key] = $this->_arraySet(implode('.', $keys), $input[$key], $value);
		}
		elseif (is_object($input))
		{
			! property_exists($input, $key) and $input->{$key} = array();
			$input[$key] = $this->_arraySet(implode('.', $keys), $input[$key], $value);
		}

		// Return the changed input back tail-recursively
		return $input;
	}
}
