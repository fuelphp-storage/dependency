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

class ResolveContextTest extends Test
{
	public function testContext()
	{
		// TODO: use mockery
		$container = new Container;

		$context = new ResolveContext($container);

		$this->assertNull($context->getName());
		$this->assertFalse($context->isMultiton());

		$context = new ResolveContext($container, 'name', true);

		$this->assertEquals('name', $context->getName());
		$this->assertTrue($context->isMultiton());

		$this->assertSame($container, $context->getContainer());
	}
}
