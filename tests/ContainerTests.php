<?php

use FuelPHP\Dependency\Container;
use FuelPHP\Dependency\Resource;

class ContainerTests extends PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testResolveFail()
	{
		$container = new Container();
		$container->resolve('unknown.dependency');
	}

	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testForgeFail()
	{
		$container = new Container();
		$container->forge('unknown.dependency');
	}

	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testAbstractFail()
	{
		$container = new Container();
		$container->forge('AbstractClass');
	}

	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testMultitonFail()
	{
		$container = new Container();
		$container->multiton('unknown.dependency');
	}

	public function testForgingProvider()
	{
		$container = new Container();
		$container->registerService(new ForgingProvider());
		$this->assertInstanceOf('stdClass', $container['forging.name']);
		$this->assertInstanceOf('stdClass', $container->forge('forging.name', array(true)));
	}

	public function testRegisteringService()
	{
		$container = new Container();
		$container->registerService(new RegisteringService());
		$this->assertInstanceOf('stdClass', $container['from.service']);
	}

	public function testInjectingService()
	{
		$container = new Container();
		$container->registerServices(array(new InjectingService()));
		$this->assertInstanceOf('FuelPHP\Dependency\ServiceProvider', $container['service']);
	}

	public function testSingletons()
	{
		$container = new Container;
		$container->registerSingleton('single', 'stdClass');
		$container->register('other', 'stdClass');
		$this->assertTrue($container['single'] === $container['single']);
		$this->assertTrue($container['single'] !== $container['other']);
		$this->assertTrue($container['single'] !== $container->forge('single'));
		$this->assertTrue($container['single'] == $container['other']);
	}

	public function testMultitons()
	{
		$container = new Container;
		$container->register('m', 'stdClass');
		$this->assertTrue($container->multiton('m', 'name') === $container->multiton('m', 'name'));
		$this->assertTrue($container->multiton('m', 'name') !== $container->multiton('m', 'other'));
		$this->assertTrue($container->multiton('m', 'name') == $container->multiton('m', 'other'));
	}

	public function testClassIdentifier()
	{
		$container = new Container;
		$this->assertInstanceOf('stdClass', $container['stdClass']);
	}

	public function testClassIdentifierForge()
	{
		$container = new Container;
		$container->registerService(new InjectingService);
		$container->registerService(new ForgingProvider);
		$this->assertInstanceOf('stdClass', $container->forge('forging.name'));
	}

	public function testOffsetExists()
	{
		$container = new Container;
		$this->assertFalse(isset($container['offset']));
		$this->assertTrue(isset($container['stdClass']));
		$container['offset'] = new stdClass;
		$this->assertTrue(isset($container['offset']));
		$container->inject('stuff', 'stuff');
		$this->assertTrue(isset($container['stuff']));
		unset($container['stuff']);
		$this->assertFalse(isset($container['stuff']));
	}

	public function testConstructorDependencies()
	{
		$container = new Container();
		$result = $container['Depending'];
		$this->assertInstanceOf('Depending', $result);
		$this->assertInstanceOf('DependedOn', $result->dep);
	}

	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testConstructorClassFail()
	{
		$container = new Container();
		$container['ConstructorFail'];
	}

	/**
	 * @expectedException FuelPHP\Dependency\ResolveException
	 */
	public function testConstructorNoClassFail()
	{
		$container = new Container();
		$container['ConstructorFailNoClass'];
	}

	public function testConstructorDefault()
	{
		$container = new Container();
		$result = $container['ConstructorDefault'];
		$this->assertNull($result->dep);
	}
}