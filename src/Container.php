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

class Container extends \League\Container\Container
{
	/**
	 * Create a new instance of alias regardless of it being singleton or not
	 *
	 * @param string $alias
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function forge($alias, array $args = [])
	{
		// invoke the correct definition
		if (array_key_exists($alias, $this->items))
		{
			return $this->resolveDefinition($alias, $args);
		}

		// if we've got this far, we can assume we need to reflect on a class
		// and automatically resolve it's dependencies, we also cache the
		// result if a caching adapter is available
		$definition = $this->reflect($alias);

		$this->items[$alias]['definition'] = $definition;

		return $definition();
	}

	/**
	 * Resolves a named instance from the container
	 *
	 * @param string $alias
	 * @param string $instance
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function multiton($alias, $instance = '__default__', array $args = [])
	{
		$name = $alias.'::'.$instance;

		// It is a singleton with a special name
		if ($this->isSingleton($name))
		{
			return $this->singletons[$name];
		}

		// Disable singleton so the resolved concrete does not gets stored
		if ($this->isRegistered($alias) and isset($this->items[$alias]['singleton']))
		{
			$previousSingletonSetting = $this->items[$alias]['singleton'];
			$this->items[$alias]['singleton'] = false;
		}

		$concrete = $this->singletons[$name] = $this->get($alias, $args);

		// Reset to the previous value
		if (isset($previousSingletonSetting))
		{
			$this->items[$alias]['singleton'] = $previousSingletonSetting;
		}

		return $concrete;
	}

	/**
	 * Checks if a resolved instance exists
	 *
	 * @param string $alias
	 * @param string $instance
	 *
	 * @return boolean
	 */
	public function isInstance($alias, $instance = null)
	{
		if (isset($instance))
		{
			$alias = $alias.'::'.$instance;
		}

		return array_key_exists($alias, $this->singletons);
	}
}
