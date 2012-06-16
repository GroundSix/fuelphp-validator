<?php

namespace Fuel\Validation;

/**
 * @backupGlobals  disabled
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$config = array(
			'valueClass' => 'bruceWillis',
			'errorClass' => 'ghost',
			'iSee' => 'deadPeople'
		);
		$val = new Base($config);

		$this->assertAttributeEquals($config, 'config', $val);
	}

	public function testValidate()
	{
		$val = new Base();
		$key = 'sense';
		$validator = function($v) { return true; };

		$val->validate($key, $validator);

		$prop = new \ReflectionProperty($val, 'validators');
		$prop->setAccessible(true);
		$validators = $prop->getValue($val);

		$this->assertArrayHasKey($key, $validators);
		list($testValidator, $testValue) = $validators[$key];

		$this->assertSame($validator, $testValidator);
		$this->assertEquals($key, $testValue->getKey());
	}

	public function testExecute()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testGetValue()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testSetValue()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testGetValidated()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testGetError()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testExecuteRule()
	{
		$this->markTestIncomplete('Unfinished');
	}
}
