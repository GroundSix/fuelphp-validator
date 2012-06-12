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

use Fuel\Validation\Value\Valuable;

/**
 * A base set of rules
 *
 * @package  Fuel\Validation
 *
 * @since  1.0.0
 */
class Base
{
	public function validateNonEmpty(Valuable $v)
	{
		if ($v->get() === null or $v->get() === '' or $v->get() === array())
		{
			$v->setError('nonEmpty');
			return false;
		}

		return true;
	}
}
