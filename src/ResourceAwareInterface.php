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
	 * Register a resource
	 *
	 * @param   string  $identier  resource identifier
	 * @param   mixed   $resource  resource
	 * @return  $this
	 */
	public function register($identier, $resource);

	/**
	 * Register a singleton resource
	 *
	 * @param   string  $identier  resource identifier
	 * @param   mixed   $resource  resource
	 * @return  $this
	 */
	public function registerSingleton($identier, $resource);

	/**
	 * Inject an instance
	 *
	 * @param   string  $identier  instance identifier
	 * @param   mixed   $instance  instance
	 * @return  $this
	 */
	public function inject($identier, $instance);

	/**
	 * Resolve an instance from a resource
	 *
	 * @param   string  $identier   resource identifier
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   resource instance
	 */
	public function resolve($identifier, array $arguments = array());

	/**
	 * Create a new instance from a resource
	 *
	 * @param   string  $identier   resource identifier
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   new resource instance
	 */
	public function forge($identifier, array $arguments = array());

	/**
	 * Resolve a named instance from a resource
	 *
	 * @param   string  $identier   resource identifier
	 * @param   string  $name       instance name
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   resource instance
	 */
	public function multiton($identifier, $name = '__default__', array $arguments = array());
}
