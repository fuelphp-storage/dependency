<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency;

use ArrayAccess;
use Closure;

class Container implements ArrayAccess, ResourceAwareInterface
{
	/**
	 * @var  array  $resources  resources
	 */
	protected $resources = array();

	/**
	 * @var  array  $instances  resolved instances
	 */
	protected $instances = array();

	/**
	 * @var  array  $services  service providers
	 */
	protected $services = array();

	/**
	 * @var  array  $extensions  resource extensions
	 */
	protected $extensions = array();

	/**
	 * Register a resource
	 *
	 * @param   string  $identifier  resource identifier
	 * @param   mixed   $resource  resource
	 * @return  $this
	 */
	public function register($identifier, $resource)
	{
		if ( ! $resource instanceof Resource)
		{
			$resource = new Resource($resource);
		}

		$this->resources[$identifier] = $resource;

		return $resource;
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
		$resource = $this->register($identifier, $resource);
		$resource->preferSingleton(true);

		return $resource;
	}

	/**
	 * Register a service provider
	 *
	 * @param   ServiceProvider  $service
	 * @return  $this
	 */
	public function registerService(ServiceProvider $service)
	{
		$service->setContainer($this);

		if ($service->provides === true)
		{
			$service->provide();
			$service->provides = false;
		}

		$this->services[get_class($service)] = $service;

		return $this;
	}

	/**
	 * Register service providers
	 *
	 * @param   ServiceProvider[] $service
	 * @return  $this
	 */
	public function registerServices(array $services)
	{
		foreach ($services as $service)
		{
			$this->registerService($service);
		}

		return $this;
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
		$this->instances[$identifier] = $instance;

		return $this;
	}

	/**
	 * Remove an instance
	 *
	 * @param   string  $identifier  instance identifier
	 * @param   mixed   $instance  instance
	 * @return  $this
	 */
	public function remove($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			unset($this->instances[$identifier]);
		}

		return $this;
	}

	protected function findResource($identifier, $arguments)
	{
		if (isset($this->resources[$identifier]))
		{
			return $this->resources[$identifier];
		}

		foreach ($this->services as $service)
		{
			if ($service->provides and in_array($identifier, $service->provides))
			{
				$service->provide();
				$service->provides = false;

				return $this->findResource($identifier, $arguments);
			}

			if ($service->handles($identifier) and $resource = $service->handle($identifier, $arguments))
			{
				return $resource;
			}
		}

		if (class_exists($identifier, true))
		{
			$this->resources[$identifier] = new Resource($identifier);

			return $this->resources[$identifier];
		}
	}

	public function find($identifier, $arguments)
	{
		if ( ! $resource = $this->findResource($identifier, $arguments))
		{
			throw new ResolveException('Could not resolve: '.$identifier);
		}

		if ( ! $resource instanceof Resource)
		{
			$resource = new Resource($resource);
		}

		return $resource;
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
		// If we find a previously resolved instance
		if ($instance = $this->getInstance($identifier))
		{
			// Return it
			return $instance;
		}

		// Find the resource
		$resource = $this->find($identifier, $arguments);

		// Resolve an instance
		$instance = $resource->resolve($this, $arguments);

		// Apply any supplied extensions
		$instance = $this->applyExtensions($identifier, $instance);

		// When the resource prefers to be Singleton
		if ($resource->preferSingleton)
		{
			// Store the instance
			$this->instances[$identifier] = $instance;
		}

		return $instance;
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
		$resource = $this->find($identifier, $arguments);
		$instance = $resource->resolve($this, $arguments);
		$instance = $this->applyExtensions($identifier, $instance);

		return $instance;
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
		$name = $identifier.'::'.$name;

		if ( ! isset($this->instances[$name]))
		{
			$this->instances[$name] = $this->forge($identifier, $arguments);
		}

		return $this->instances[$name];
	}

	public function extend($identifier, Closure $extension)
	{
		if ( ! isset($this->extensions[$identifier]))
		{
			$this->extensions[$identifier] = array();
		}

		$this->extensions[$identifier][] = $extension;

		return $this;
	}

	public function applyExtensions($identifier, $instance)
	{
		if ( ! isset($this->extensions[$identifier]))
		{
			return $instance;
		}

		foreach ($this->extensions[$identifier] as $extension)
		{
			if ($result = $extension($this, $instance))
			{
				$instance = $result;
			}
		}

		return $instance;
	}

	/**
	 * Retrieve a resolved instance
	 *
	 * @param   string      $identifier  instance identifier
	 * @return  mixed|null  instance or null
	 */
	protected function getInstance($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			return $this->instances[$identifier];
		}
	}

	public function offsetExists($offset)
	{
		if ($this->getInstance($offset) or $this->findResource($offset, array()))
		{
			return true;
		}

		return false;
	}

	public function offsetGet($offset)
	{
		return $this->resolve($offset);
	}

	public function offsetSet($offset, $resource)
	{
		$this->register($offset, $resource);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
}
