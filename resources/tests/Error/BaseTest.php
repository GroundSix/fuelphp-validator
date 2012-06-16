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

	public function testGetMessage()
	{
		$msg = 'whoHasTheRing';
		$translation = 'Frodo';

		$v = $this->getMock('Fuel\\Validation\\Base');
		$v->expects($this->once())
			->method('getMessage')
			->with($this->equalTo($msg))
			->will($this->returnValue($translation));

		$val = $this->getMockBuilder('Fuel\\Validation\\Value\\Base')
			->disableOriginalConstructor()
			->getMock();
		$val->expects($this->once())
			->method('getValidation')
			->will($this->returnValue($v));

		$error = new Base($val, $msg);
		$this->assertEquals($translation, $error->getMessage());
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
		$val = $this->getMockBuilder('Fuel\\Validation\\Value\\Base')
			->disableOriginalConstructor()
			->getMock();

		$msg = uniqid();
		$error = new Base($val, $msg);

		$this->assertEquals($val, $error->getValue());
	}

	public function testToString()
	{
		$msg = 'whoHadTheRing';
		$translation = 'Smeagol';

		$v = $this->getMock('Fuel\\Validation\\Base');
		$v->expects($this->once())
			->method('getMessage')
			->with($this->equalTo($msg))
			->will($this->returnValue($translation));

		$val = $this->getMockBuilder('Fuel\\Validation\\Value\\Base')
			->disableOriginalConstructor()
			->getMock();
		$val->expects($this->once())
			->method('getValidation')
			->will($this->returnValue($v));

		$error = new Base($val, $msg);
		$this->assertEquals($translation, strval($error));
	}
}
