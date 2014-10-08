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

use ArrayAccess;
use Closure;

class Container implements ArrayAccess, ResourceAwareInterface
{
	/**
	 * @var array $resources
	 */
	protected $resources = [];

	/**
	 * @var array $instances
	 */
	protected $instances = [];

	/**
	 * @var ServiceProvider[]  $services
	 */
	protected $services = [];

	/**
	 * Resource specific extensions
	 *
	 * @var array $extends
	 */
	protected $extends = [];

	/**
	 * Resource generic and reusable extensions
	 *
	 * @var array $extensions
	 */
	protected $extensions = [];

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function registerSingleton($identifier, $resource)
	{
		$resource = $this->register($identifier, $resource);
		$resource->preferSingleton(true);

		return $resource;
	}

	/**
	 * Registers a service provider
	 *
	 * @param ServiceProvider $service
	 *
	 * @return $this
	 */
	public function registerService(ServiceProvider $service)
	{
		$service->setContainer($this);

		// The provider does not contain a list of resources...
		if ($service->provides === true)
		{
			// ...so we fetch them all here...
			$service->provide();

			// ...and prevent it from re-fetching in the future
			$service->provides = false;
		}

		$this->services[get_class($service)] = $service;

		return $this;
	}

	/**
	 * Registers service providers
	 *
	 * @param ServiceProvider[] $services
	 *
	 * @return $this
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
	 * {@inheritdoc}
	 */
	public function inject($identifier, $instance)
	{
		$this->instances[$identifier] = $instance;

		return $this;
	}

	/**
	 * Removes an instance
	 *
	 * @param string $identifier
	 *
	 * @return $this
	 */
	public function remove($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			unset($this->instances[$identifier]);
		}

		return $this;
	}

	/**
	 * Finds a resource identified by the identifier passed
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed The found resource, or null if not found
	 */
	protected function findResource($identifier, $arguments)
	{
		if (isset($this->resources[$identifier]))
		{
			return $this->resources[$identifier];
		}

		foreach ($this->services as $service)
		{
			/** @type ServiceProvider $service */
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
			return $this->resources[$identifier] = new Resource($identifier);
		}

		return null;
	}

	/**
	 * Finds and returns a new instance of a resource
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed The found resource, or null if not found
	 *
	 * @throws ResolveException If the identifier can not be resolved
	 */
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
	 * {@inheritdoc}
	 */
	public function resolve($identifier, array $arguments = [])
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
	 * {@inheritdoc}
	 */
	public function forge($identifier, array $arguments = [])
	{
		$resource = $this->find($identifier, $arguments);
		$instance = $resource->resolve($this, $arguments);
		$instance = $this->applyExtensions($identifier, $instance);

		return $instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function multiton($identifier, $name = '__default__', array $arguments = [])
	{
		$name = $identifier.'::'.$name;

		if ( ! isset($this->instances[$name]))
		{
			$this->instances[$name] = $this->forge($identifier, $arguments);
		}

		return $this->instances[$name];
	}

	/**
	 * Attaches extensions to an identifier
	 *
	 * @param string          $identifier
	 * @param string|Closure  $extension  the generic extension, or a closure implementing the extension
	 *
	 * @return $this
	 */
	public function extend($identifier, $extension)
	{
		if ( ! isset($this->extends[$identifier]))
		{
			$this->extends[$identifier] = [];
		}

		$this->extends[$identifier][] = $extension;

		return $this;
	}

	/**
	 * Defines a generic resource extension
	 *
	 * @param string  $identifier
	 * @param Closure $extension
	 *
	 * @return $this
	 */
	public function extension($identifier, Closure $extension)
	{
		$this->extensions[$identifier] = $extension;

		return $this;
	}

	/**
	 * Applies all defined extensions to the instance
	 *
	 * @param string $identifier
	 * @param mixed  $instance
	 *
	 * @return mixed
	 */
	public function applyExtensions($identifier, $instance)
	{
		if ( ! isset($this->extends[$identifier]))
		{
			return $instance;
		}

		foreach ($this->extends[$identifier] as $extension)
		{
			if (is_string($extension) and isset($this->extensions[$extension]))
			{
				$extension = $this->extensions[$extension];
			}

			if ($result = $extension($this, $instance))
			{
				$instance = $result;
			}
		}

		return $instance;
	}

	/**
	 * Retrieves a resolved instance
	 *
	 * @param string $identifier
	 *
	 * @return mixed|null
	 */
	protected function getInstance($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			return $this->instances[$identifier];
		}
	}

	/**
	 * Check if a resolved instance exists
	 *
	 * @param string $identifier
	 *
	 * @return mixed|null
	 */
	public function isInstance($identifier, $name = null)
	{
		if ($name !== null)
		{
			$identifier = $identifier.'::'.$name;
		}
		return isset($this->instances[$identifier]);
	}

	public function offsetExists($offset)
	{
		if ($this->getInstance($offset) or $this->findResource($offset, []))
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
