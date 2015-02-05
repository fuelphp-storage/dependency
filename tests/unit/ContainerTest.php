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

class ContainerTest extends Test
{
	/**
	 * @var Container
	 */
	protected $container;

	protected function _before()
	{
		$this->container = new Container;
	}

	public function testMultiton()
	{
		$this->container->add('m', 'stdClass');
		$this->assertTrue($this->container->multiton('m', 'name') === $this->container->multiton('m', 'name'));
		$this->assertTrue($this->container->multiton('m', 'name') !== $this->container->multiton('m', 'other'));
		$this->assertTrue($this->container->multiton('m', 'name') == $this->container->multiton('m', 'other'));
		$this->assertTrue($this->container->isInstance('m', 'name'));
	}

	// public function testExtends()
	// {
	// 	$container = new Container;
	// 	$container->register('id', 'stdClass');

	// 	$container->extend('id', function($container, $instance) {
	// 		$instance->name = 'Frank';
	// 	});

	// 	$container->extend('id', function($container, $instance) {
	// 		$instance->surname = 'de Jonge';

	// 		return $instance;
	// 	});

	// 	$instance = $container['id'];

	// 	$this->assertEquals('Frank', $instance->name);
	// 	$this->assertEquals('de Jonge', $instance->surname);
	// }

	// public function testExtendsMultiton()
	// {
	// 	$container = new Container;
	// 	$container->register('id', 'stdClass');

	// 	$container->extend('id', function($container, $instance) {
	// 		$instance->name = 'Frank';
	// 	});

	// 	$container->extendMultiton('id', 'fullname', function($container, $instance) {
	// 		$instance->surname = 'de Jonge';

	// 		return $instance;
	// 	});

	// 	$instance = $container['id'];

	// 	$this->assertEquals('Frank', $instance->name);
	// 	$this->assertObjectNotHasAttribute('surname', $instance);

	// 	$instance = $container->multiton('id', 'fullname');

	// 	$this->assertEquals('Frank', $instance->name);
	// 	$this->assertEquals('de Jonge', $instance->surname);
	// }

	// public function testExtensions()
	// {
	// 	$container = new Container;
	// 	$container->register('id1', 'stdClass');
	// 	$container->register('id2', 'stdClass');

	// 	$container->extension('addName', function($container, $instance) {
	// 		$instance->name = 'Frank';
	// 		$instance->surname = 'de Jonge';
	// 	});

	// 	$container->extend('id1', 'addName');
	// 	$container->extend('id2', 'addName');

	// 	$container->extension('addSurname', function($container, $instance) {
	// 		$instance->surname = 'de Oude';

	// 		return $instance;
	// 	});

	// 	$container->extend('id1', 'addSurname');

	// 	$instance = $container['id1'];
	// 	$this->assertEquals('Frank', $instance->name);
	// 	$this->assertEquals('de Oude', $instance->surname);

	// 	$instance = $container['id2'];
	// 	$this->assertEquals('Frank', $instance->name);
	// 	$this->assertEquals('de Jonge', $instance->surname);
	// }

	/**
	 * @expectedException \Fuel\Dependency\InvalidExtensionException
	 */
	// public function testExtendsFailure()
	// {
	// 	$container = new Container;
	// 	$container->register('id', 'stdClass');

	// 	$container->extend('id', 'this_is_not_a_callable');

	// 	$container['id'];
	// }
}
