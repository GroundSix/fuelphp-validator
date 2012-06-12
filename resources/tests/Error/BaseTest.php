<?php

namespace Fuel\Validation\Error;

/**
 * @backupGlobals  disabled
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$val = $this->getMockBuilder('Fuel\\Validation\\Value\\Base')
			->disableOriginalConstructor()
			->getMock();

		$msg = uniqid();
		$error = new Base($val, $msg);

		$this->assertAttributeEquals($msg, 'message', $error);
		$this->assertAttributeEquals($val, 'value', $error);

		return array($error, $msg);
	}

	/**
	 * @depends  testConstructor
	 */
	public function testGetMessage($errormsg)
	{
		list($error, $msg) = $errormsg;
		$this->assertEquals($msg, $error->getMessage());
	}

	public function testGetKey()
	{
		$key = uniqid();
		$val = $this->getMockBuilder('Fuel\\Validation\\Value\\Base')
			->disableOriginalConstructor()
			->getMock();
		$val->expects($this->once())
			->method('getKey')
			->will($this->returnValue($key));

		$error = new Base($val, 'message');

		$this->assertEquals($key, $error->getKey());
	}

	public function testGetValue()
	{
		$this->markTestIncomplete('Unfinished');
	}

	public function testToString()
	{
		$this->markTestIncomplete('Unfinished');
	}
}
