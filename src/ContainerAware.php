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

/**
 * Implements container aware logic
 *
 * Classes using this class should implement ResourceAwareInterface
 */
trait ContainerAware
{
	/**
	 * @var Container
	 */
	protected $container;

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
	 * Attaches extensions to a multiton identifier
	 *
	 * @param string         $identifier
	 * @param string         $name
	 * @param string|Closure $extension  the generic extension, or a closure implementing the extension
	 *
	 * @return $this
	 */
	public function extendMultiton($identifier, $name, $extension)
	{
		return $this->container->extendMultiton($identifier, $name, $extension);
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
