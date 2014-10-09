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

use Closure;

abstract class ServiceProvider implements ResourceAwareInterface
{
	use ContainerAware;

	/**
	 * @var string $namespace
	 */
	public $namespace;

	/**
	 * Provides list of identifiers
	 *
	 * @var array|boolean
	 */
	public $provides;

	/**
	 * Checks weather the the identifier is handles by the service provider
	 *
	 * @param string $identifier
	 *
	 * @return boolean
	 */
	public function handles($identifier)
	{
		if ( ! $this->namespace)
		{
			return false;
		}

		return (strpos($identifier, $this->namespace) === 0);
	}

	/**
	 * Handles creating a new instance
	 *
	 * @param string $identifier
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function handle($identifier, array $arguments)
	{
		$name = substr($identifier, strlen($this->namespace)+1);

		return $this->forge($name, $arguments);
	}
}
