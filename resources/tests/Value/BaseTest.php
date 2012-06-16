<?php

namespace Fuel\Validation\Value;

/**
 * @backupGlobals  disabled
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$val = $this->getMock('Fuel\\Validation\\Base');
		$key = 'test';
		$value = new Base($val, $key);

		$this->assertAttributeSame($val, 'validation', $value);
		$this->assertAttributeSame($key, 'key', $value);
	}

	public function testGet()
	{
		$val = $this->getMock('Fuel\\Validation\\Base', array('getValue'));
		$key = 'test';
		$value = new Base($val, $key);
		$actualVal = 'success';

		$val->expects($this->once())
			->method('getValue')
			->with($this->equalTo($key))
			->will($this->returnValue($actualVal));

		$this->assertEquals($actualVal, $value->get());
	}

	public function testSet()
	{
		$val = $this->getMock('Fuel\\Validation\\Base', array('setValue'));
		$key = 'test';
		$value = new Base($val, $key);
		$newVal = 'success';

		$val->expects($this->once())
			->method('setValue')
			->with($this->equalTo($key), $this->equalTo($newVal));

		$this->assertEquals($value, $value->set($newVal));
	}

	public function testGetKey()
	{
		$val = $this->getMock('Fuel\\Validation\\Base');
		$key = 'test';
		$value = new Base($val, $key);

		$this->assertEquals($key, $value->getKey());
	}

	public function testSetKey()
	{
		$val = $this->getMock('Fuel\\Validation\\Base');
		$key = 'test';
		$newKey = 'some';
		$value = new Base($val, $key);

		$this->assertAttributeEquals($key, 'key', $value);
		$this->assertEquals($value, $value->setKey($newKey));
		$this->assertAttributeEquals($newKey, 'key', $value);
	}

	public function testSetError()
	{
		$value = new Base($this->getMock('Fuel\\Validation\\Base'), 'alcohol');
		$error = 'charlieSheen';

		$this->assertAttributeEquals(null, 'error', $value);
		$this->assertEquals($value, $value->setError($error));
		$this->assertAttributeEquals($error, 'error', $value);

		return $value;
	}

	/**
	 * @depends  testSetError
	 */
	public function testGetError(Valuable $value)
	{
		$this->assertEquals('charlieSheen', $value->getError());

		return $value;
	}

	/**
	 * @depends  testGetError
	 */
	public function testResetError(Valuable $value)
	{
		$this->assertSame($value, $value->resetError());
		$this->assertAttributeEquals(null, 'error', $value);
	}

	public function testValidates()
	{
		$value = new Base($this->getMock('Fuel\\Validation\\Base'), 'alcohol');
		$error = 'charlieSheen';

		$this->assertTrue($value->validates());
		$value->setError($error);
		$this->assertFalse($value->validates());
		$value->resetError();
		$this->assertTrue($value->validates());
	}

	public function testGetValidation()
	{
		$val = $this->getMock('Fuel\\Validation\\Base');
		$value = new Base($val, 'something');

		$this->assertSame($val, $value->getValidation());
	}

	public function testCall()
	{
		$val = $this->getMock('Fuel\\Validation\\Base', array('executeRule'));
		$value = new Base($val, 'test');

		$returnVal = 'success';
		$method = 'iKnowWhatYouDid';
		$args = array('last', 'summer');

		$val->expects($this->once())
			->method('executeRule')
			->with($this->equalTo($method), $this->equalTo($value), $this->equalTo($args))
			->will($this->returnValue($returnVal));

		$this->assertEquals($returnVal, call_user_func_array(array($value, $method), $args));
	}
}
