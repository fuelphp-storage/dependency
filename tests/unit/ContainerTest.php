<?php

namespace Fuel\Dependency;

use Codeception\TestCase\Test;

class ContainerTest extends Test
{
	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testResolveFail()
	{
		$container = new Container();
		$container->resolve('unknown.dependency');
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testForgeFail()
	{
		$container = new Container();
		$container->forge('unknown.dependency');
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testAbstractFail()
	{
		$container = new Container();
		$container->forge('AbstractClass');
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testMultitonFail()
	{
		$container = new Container();
		$container->multiton('unknown.dependency');
		$container->isInstance('unknown','dependency');
	}

	public function testForgingProvider()
	{
		$container = new Container();
		$container->registerService(new \ForgingProvider());
		$this->assertInstanceOf('stdClass', $container['forging.name']);
		$this->assertInstanceOf('stdClass', $container->forge('forging.name', array(true)));
	}

	public function testRegisteringService()
	{
		$container = new Container();
		$container->registerService(new \RegisteringService());
		$this->assertInstanceOf('stdClass', $container['from.service']);
		$this->assertEquals('This Works!', $container['from.service']->forge->extension);
	}

	public function testExtensionService()
	{
		$container = new Container();
		$container->registerService(new \ExtensionService());
		$container->register('id', 'stdClass');
		$container->extend('id', 'extension');
		$instance = $container['id'];
		$this->assertEquals('This Works!', $instance->extension);
	}

	public function testInjectingService()
	{
		$container = new Container();
		$container->registerServices(array(new \InjectingService()));
		$this->assertInstanceOf('Fuel\Dependency\ServiceProvider', $container['service']);
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
		$this->assertTrue($container->isInstance('single'));
	}

	public function testMultitons()
	{
		$container = new Container;
		$container->register('m', 'stdClass');
		$this->assertTrue($container->multiton('m', 'name') === $container->multiton('m', 'name'));
		$this->assertTrue($container->multiton('m', 'name') !== $container->multiton('m', 'other'));
		$this->assertTrue($container->multiton('m', 'name') == $container->multiton('m', 'other'));
		$this->assertTrue($container->isInstance('m', 'name'));
	}

	public function testClassIdentifier()
	{
		$container = new Container;
		$this->assertInstanceOf('stdClass', $container['stdClass']);
	}

	public function testClassIdentifierForge()
	{
		$container = new Container;
		$container->registerService(new \InjectingService);
		$container->registerService(new \ForgingProvider);
		$this->assertInstanceOf('stdClass', $container->forge('forging.name'));
	}

	public function testOffsetExists()
	{
		$container = new Container;
		$this->assertFalse(isset($container['offset']));
		$this->assertTrue(isset($container['stdClass']));
		$container['offset'] = new \stdClass;
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
	 * @expectedException \Fuel\Dependency\ResolveException
	 */
	public function testConstructorClassFail()
	{
		$container = new Container();
		$container['ConstructorFail'];
	}

	/**
	 * @expectedException \Fuel\Dependency\ResolveException
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

	public function testExtends()
	{
		$container = new Container;
		$container->register('id', 'stdClass');

		$container->extend('id', function($container, $instance) {
			$instance->name = 'Frank';
		});

		$container->extend('id', function($container, $instance) {
			$instance->surname = 'de Jonge';

			return $instance;
		});

		$instance = $container['id'];

		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Jonge', $instance->surname);
	}

	public function testExtensions()
	{
		$container = new Container;
		$container->register('id1', 'stdClass');
		$container->register('id2', 'stdClass');

		$container->extension('addName', function($container, $instance) {
			$instance->name = 'Frank';
			$instance->surname = 'de Jonge';
		});

		$container->extend('id1', 'addName');
		$container->extend('id2', 'addName');

		$container->extension('addSurname', function($container, $instance) {
			$instance->surname = 'de Oude';

			return $instance;
		});

		$container->extend('id1', 'addSurname');

		$instance = $container['id1'];
		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Oude', $instance->surname);

		$instance = $container['id2'];
		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Jonge', $instance->surname);
	}
}
