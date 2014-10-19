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

abstract class ServiceProvider implements ResourceAwareInterface
{
	use ContainerAware;

	/**
	 * Provides list of identifiers
	 *
	 * @var array|boolean
	 */
	public $provides;
}
