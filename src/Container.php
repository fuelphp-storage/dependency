<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency;

/**
 * Dependency container
 *
 * @package Fuel\Dependency
 *
 * @since 2.0
 */
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
		return $this->get($alias, $args);
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
		if (1 === func_num_args())
		{
			$multitons = [];
			foreach ($this->sharedDefinitions as $name => $value)
			{
				if (0 === strpos($name, $alias.'::'))
				{
					$multitons[substr($name, strlen($alias)+2)] = $value;
				}
			}
			return $multitons;
		}

		$name = $alias.'::'.$instance;

		// It is a singleton with a special name
		if ($this->hasShared($name))
		{
			return $this->sharedDefinitions[$name];
		}

		// Disable singleton so the resolved concrete does not gets stored
		if ($this->has($alias) and isset($this->sharedDefinitions[$alias]))
		{
			$previousSingletonSetting = $this->sharedDefinitions[$alias];
			unset($this->sharedDefinitions[$alias]);
			$this->definitions[$alias] = $previousSingletonSetting;
		}

		$concrete = $this->sharedDefinitions[$name] = $this->get($alias, $args);

		// Reset to the previous value
		if (isset($previousSingletonSetting))
		{
			unset($this->definitions[$alias]);
			$this->sharedDefinitions[$alias] = $previousSingletonSetting;
		}

		return $concrete;
	}
}
