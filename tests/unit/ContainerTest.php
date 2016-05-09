<?php

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

	public function testMultiton()
	{
		$this->container->add('m', 'stdClass', true);
		$this->assertSame($this->container->multiton('m', 'name'), $this->container->multiton('m', 'name'));
		$this->assertNotSame($this->container->multiton('m', 'name'), $this->container->multiton('m', 'other'));
		$this->assertEquals($this->container->multiton('m', 'name'), $this->container->multiton('m', 'other'));

		$this->assertEquals(
			[
				'name' => new \stdClass(),
				'other' => new \stdClass(),
			],
			$this->container->multiton('m')
		);
	}
}
