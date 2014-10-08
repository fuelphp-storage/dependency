<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency;

use Codeception\TestCase\Test;
use stdClass;

class ResourceTest extends Test
{
	public function testPreferSingleton()
	{
		$resource = new Resource(null);

		$this->assertFalse($resource->preferSingleton);
		$this->assertSame($resource, $resource->preferSingleton());
		$this->assertTrue($resource->preferSingleton);
	}

	/**
	 * @dataProvider argumentProvider
	 */
	public function testResolveCallable()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource(function($dic) {
			return new stdClass;
		});

		$arguments = func_get_args();

		$instance = $resource->resolve($container, $arguments);

		$this->assertInstanceOf('stdClass', $instance);
	}

	public function argumentProvider()
	{
		return [
			0 => [],
			1 => [null],
			2 => [null, null],
			3 => [null, null, null],
			4 => [null, null, null, null],
			5 => [null, null, null, null, null],
		];
	}

	public function testResolveSimpleClass()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource('stdClass');

		$instance = $resource->resolve($container);

		$this->assertInstanceOf('stdClass', $instance);
	}

	public function testConstructorDependencies()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource('Depending');

		$instance = $resource->resolve($container);

		$this->assertInstanceOf('Depending', $instance);
		$this->assertInstanceOf('DependedOn', $instance->dep);
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testConstructorClassFail()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource('ConstructorFail');

		$resource->resolve($container);
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testConstructorNoClassFail()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource('ConstructorFailNoClass');

		$resource->resolve($container);
	}

	public function testConstructorDefault()
	{
		// TODO: use mockery
		$container = new Container;

		$resource = new Resource('ConstructorDefault');

		$instance = $resource->resolve($container);

		$this->assertNull($instance->dep);
	}
}
