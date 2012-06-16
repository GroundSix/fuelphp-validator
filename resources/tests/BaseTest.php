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

	public function testExecuteWithWildcard()
	{
		$v = new Base();

		$v->validate('users.*.username', function(Value\Valuable $v) {
			return $v->require() and $v->atLeastChars(4);
		})->validate('users.*.password', function (Value\Valuable $v) {
			return $v->require() and $v->atLeastChars(4);
		});

		$input = array(
			'users' => array(
				0 => array(
					'username' => 'test',
					'password' => '123456',
				),
				1 => array(
					'username' => 'est',
					'password' => '123478',
				),
				2 => array(
					'username' => 'vest',
					'password' => '12',
				),
				3 => array(
					'password' => '1234',
				),
			),
		);

		$validates = array(
			'users' => array(
				0 => array(
					'username' => 'test',
					'password' => '123456',
				),
				1 => array(
					'password' => '123478',
				),
				2 => array(
					'username' => 'vest',
				),
				3 => array(
					'password' => '1234',
				),
			),
		);

		$errors = array(
			'users.1.username' => 'atLeastChars',
			'users.3.username' => 'nonEmpty',
			'users.2.password' => 'atLeastChars',
		);

		$this->assertFalse($v->execute($input));
		$this->assertEquals($validates, $v->getValidated());
		foreach ($errors as $ek => $ev)
		{
			$this->assertEquals($ev, strval($v->getError($ek)));
		}
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
