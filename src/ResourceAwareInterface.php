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

interface ResourceAwareInterface
{
	/**
	 * Registers a resource
	 *
	 * @param string $identifier
	 * @param mixed  $resource
	 *
	 * @return $this
	 */
	public function register($identifier, $resource);

	/**
	 * Registers a singleton resource
	 *
	 * @param string $identifier
	 * @param mixed  $resource
	 *
	 * @return $this
	 */
	public function registerSingleton($identifier, $resource);

	/**
	 * Injects an instance
	 *
	 * @param string $identifier
	 * @param mixed  $instance
	 *
	 * @return $this
	 */
	public function inject($identifier, $instance);

	/**
	 * Resolves an instance from a resource
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function resolve($identifier, array $arguments = []);

	/**
	 * Creates a new instance from a resource
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function forge($identifier, array $arguments = []);

	/**
	 * Resolves a named instance from a resource
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function multiton($identifier, $name = '__default__', array $arguments = []);
}
