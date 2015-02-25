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

	public function testForge()
	{
		$this->container->add('m', 'stdClass');

		$this->assertNotSame($this->container->forge('m'), $this->container->forge('m'));
	}

	public function testForgeReflect()
	{
		$this->assertNotSame($this->container->forge('stdClass'), $this->container->forge('stdClass'));
	}

	public function testMultiton()
	{
		$this->container->add('m', 'stdClass', true);
		$this->assertSame($this->container->multiton('m', 'name'), $this->container->multiton('m', 'name'));
		$this->assertNotSame($this->container->multiton('m', 'name'), $this->container->multiton('m', 'other'));
		$this->assertEquals($this->container->multiton('m', 'name'), $this->container->multiton('m', 'other'));
		$this->assertTrue($this->container->isInstance('m', 'name'));
	}

	public function testIsInstance()
	{
		$this->container->add('m', 'stdClass', true);

		$this->assertFalse($this->container->isInstance('m'));
		$this->container->get('m');
		$this->assertTrue($this->container->isInstance('m'));

		$this->assertFalse($this->container->isInstance('m', 'test'));
		$this->container->multiton('m', 'test');
		$this->assertTrue($this->container->isInstance('m', 'test'));
	}
}
