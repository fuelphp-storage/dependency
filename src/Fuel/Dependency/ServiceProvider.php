<?php
/**
 * @package    Fuel\Foundation
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency;

abstract class ServiceProvider implements ResourceAwareInterface
{
	/**
	 * @var  string  $namespace  provider namespace
	 */
	public $namespace;

	/**
	 * @var  Container  $container
	 */
	protected $container;

	/**
	 * @var  array  @provides  list of identifiers
	 */
	public $provides;

	/**
	 * Container injection
	 *
	 * @param   Container  $container  container
	 * @return  $this
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * Check wether the the identifier is handles by the service provider
	 *
	 * @param   string   $identifier
	 * @return  boolean  wether the identifier is handled by the provider
	 */
	public function handles($identifier)
	{
		if ( ! $this->namespace)
		{
			return false;
		}

		return (strpos($identifier, $this->namespace) === 0);
	}

	public function handle($identifier, $arguments)
	{
		$name = substr($this->namespace, strlen($this->namespace));

		return $this->factory($name, $arguments);
	}

	/**
	 * Register a resource
	 *
	 * @param   string  $identifier  resource identifier
	 * @param   mixed   $resource  resource
	 * @return  $this
	 */
	public function register($identifier, $resource)
	{
		$this->container->register($identifier, $resource);

		return $this;
	}

	/**
	 * Register a singleton resource
	 *
	 * @param   string  $identifier  resource identifier
	 * @param   mixed   $resource  resource
	 * @return  $this
	 */
	public function registerSingleton($identifier, $resource)
	{
		$this->container->registerSingleton($identifier, $resource);

		return $this;
	}

	/**
	 * Resolve an instance from a resource
	 *
	 * @param   string  $identifier   resource identifier
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   resource instance
	 */
	public function resolve($identifier, array $arguments = array())
	{
		return $this->container->resolve($identifier, $arguments);
	}

	/**
	 * Resolve a named instance from a resource
	 *
	 * @param   string  $identifier   resource identifier
	 * @param   string  $name       instance name
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   resource instance
	 */
	public function multiton($identifier, $name = '__default__', array $arguments = array())
	{
		return $this->container->multiton($identifier, $name, $arguments);
	}

	/**
	 * Convert a resource instance to a Singleton Resource
	 *
	 * @param   mixed      $instance  resource instance
	 * @return  Singleton  singleton resource
	 */
	public function singleton($instance)
	{
		if ($instance instanceof Resource)
		{
			$instance = $instance->translation;
		}

		return new Singleton($instance);
	}

	/**
	 * Inject an instance
	 *
	 * @param   string  $identifier  instance identifier
	 * @param   mixed   $instance  instance
	 * @return  $this
	 */
	public function inject($identifier, $instance)
	{
		$this->container->registerSingleton($identifier, $instance);

		return $this;
	}

	/**
	 * Create a new instance from a resource
	 *
	 * @param   string  $identifier   resource identifier
	 * @param   array   $arguments  constructor arguments
	 * @return  mixed   new resource instance
	 */
	public function forge($identifier, array $arguments = array())
	{
		$this->container->forge($identifier, $arguments);
	}
}
