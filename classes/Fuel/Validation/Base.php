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
		'valueClass' => 'Fuel\\Validation\\Value\\Base',
	);

	/**
	 * @var  array  classes and objects to search for rules
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
	 */
	protected $input;

	/**
	 * @var  array  Error\Errorable
	 */
	protected $errors = array();

	/**
	 * Constructor
	 *
	 * @param  array  $config
	 */
	public function __construct(array $config = array())
	{
		$this->config = $config;
		$this->ruleSets[] = new RuleSet\Base();
	}

	/**
	 * Add a validator for a key
	 *
	 * @param             $value
	 * @param   \Closure  $validator
	 * @return  Base
	 *
	 * @since  1.0.0
	 */
	public function validate($value, Closure $validator)
	{
		if ( ! $value instanceof Value\Valuable)
		{
			$class = $this->config['valueClass'];
			$value = new $class($this, $value);
		}

		$this->validators[$value->getKey()] = array($validator, $value);
		return $this;
	}

	/**
	 * Executes the validation
	 *
	 * @param   $input
	 * @return  bool
	 *
	 * @since  1.0.0
	 */
	public function execute( & $input)
	{
		// Assign the input by reference
		$this->input =& $input;

		// Iterate over the validators
		foreach ($this->validators as $validation)
		{
			list($validator, $value) = $validation;
			$validator($value);

			if ( ! $value->validates())
			{
				$this->errors[$value->getKey()] = $value->getError();
			}
		}

		return empty($this->errors);
	}

	/**
	 * Returns an input key by reference
	 *
	 * @param   null|string  $key
	 * @param   mixed        $default
	 * @return  mixed
	 * @throws  \RuntimeException
	 */
	public function & getInput($key = null, $default = null)
	{
		if (is_null($this->input))
		{
			throw new \RuntimeException('Validation needs to run before input is available.');
		}

		// No args? Return the whole thing
		if (func_num_args() === 0)
		{
			return $this->input;
		}

		$keys  =  explode('.', $key);
		$input =& $this->input;
		foreach ($keys as $key)
		{
			if (is_array($input) or $input instanceof ArrayAccess)
			{
				if (isset($input[$key]))
				{
					$input =& $input[$key];
					continue;
				}
			}
			elseif (is_object($input))
			{
				if (property_exists($input, $key))
				{
					$input =& $input->{$key};
					continue;
				}
			}

			// still here? key doesn't exist
			return $default;
		}

		return $input;
	}

	/**
	 * Modify a specific key in the input, will create arrays when necessary
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  Base
	 * @throws  \RuntimeException
	 *
	 * @since  1.0.0
	 */
	public function setInput($key, $value)
	{
		if (is_null($this->input))
		{
			throw new \RuntimeException('Validation needs to run before input is available.');
		}

		$keys  =  explode('.', $key);
		$input =& $this->input;
		foreach ($keys as $key)
		{
			if (is_array($input) or $input instanceof ArrayAccess)
			{
				! isset($input[$key]) and $input[$key] = array();
				$input =& $input[$key];
			}
			elseif (is_object($input))
			{
				! property_exists($input, $key) and $input->{$key} = array();
				$input =& $input->{$key};
			}
		}

		// Set the value
		$input = $value;

		return $this;
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
}
