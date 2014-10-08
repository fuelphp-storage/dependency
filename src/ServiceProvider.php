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
	/**
	 * @var string $namespace
	 */
	public $namespace;

	/**
	 * @var Container $container
	 */
	protected $container;

	/**
	 * Provides list of identifiers
	 *
	 * @var array|boolean
	 */
	public $provides;

	/**
	 * Sets the container
	 *
	 * @param Container $container
	 *
	 * @return $this
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}

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

	/**
	 * {@inheritdoc}
	 */
	public function register($identifier, $resource)
	{
		$this->container->register($identifier, $resource);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerSingleton($identifier, $resource)
	{
		$this->container->registerSingleton($identifier, $resource);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function resolve($identifier, array $arguments = [])
	{
		return $this->container->resolve($identifier, $arguments);
	}

	/**
	 * {@inheritdoc}
	 */
	public function multiton($identifier, $name = '__default__', array $arguments = [])
	{
		return $this->container->multiton($identifier, $name, $arguments);
	}

	/**
	 * Convert a resource instance to a Singleton Resource
	 *
	 * @param   mixed      $instance  resource instance
	 *
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
	 * {@inheritdoc}
	 */
	public function inject($identifier, $instance)
	{
		$this->container->registerSingleton($identifier, $instance);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function forge($identifier, array $arguments = [])
	{
		return $this->container->forge($identifier, $arguments);
	}

	/**
	 * Attaches extensions to an identifier
	 *
	 * @param string         $identifier
	 * @param string|Closure $extension  the generic extension, or a closure implementing the extension
	 *
	 * @return $this
	 */
	public function extend($identifier, $extension)
	{
		$this->container->extend($identifier, $extension);

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
		$this->container->extension($identifier, $extension);

		return $this;
	}
}
