<?php

namespace FuelPHP\Dependency;

use ArrayAccess;

class Container implements ArrayAccess, ResourceAwareInterface
{
	protected $resources = array();

	protected $instances = array();

	protected $services = array();

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

	public function registerService(ServiceProvider $service)
	{
		$service->setContainer($this);

		if ($service->provides === true)
		{
			$service->doProvide();
		}

		$this->services[get_class($service)] = $service;

		return $this;
	}

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

	protected function find($identifier, $arguments)
	{
		if (isset($this->resources[$identifier]))
		{
			return $this->resources[$identifier];
		}

		foreach ($this->services as $service)
		{
			if ($service->provides and in_array($identifier, $service->provides))
			{
				$service->doProvide();

				return $this->find($identifier, $arguments);
			}

			if ($service->handles($identifier) and $resource = $service->handle($identifier, $arguments))
			{
				return $resource;
			}
		}

		if (class_exists($identifier))
		{
			return new Resource($identifier);
		}
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
		if ($instance = $this->getInstance($identifier)) {
			return $instance;
		}

		if ( ! $resource = $this->find($identifier, $arguments)) {
			throw new ResolveException('Could not resolve: '.$identifier);
		}

		if ( ! $resource instanceof Resource) {
			$resource = new Resource($resource);
		}

		$instance = $resource->resolve($this, $arguments);

		if ($resource->preferSingleton) {
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
		if ( ! $resource = $this->find($identifier, $arguments))
		{
			throw new ResolveException('Could not resolve: '.$identifier);
		}

		if ( ! $resource instanceof Resource) {
			$resource = new Resource($resource);
		}

		return $resource->resolve($this, $arguments);
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

	protected function getInstance($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			return $this->instances[$identifier];
		}
	}

	public function offsetExists($offset)
	{
		if ($this->getInstance($offset) or $this->find($offset, array()))
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