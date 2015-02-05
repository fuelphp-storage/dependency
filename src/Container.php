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
		if ($this->isSingleton($name)) {
			return $this->singletons[$name];
		}

		// Disable singleton so the resolved concrete does not gets stored
		if ($this->isRegistered($alias) and isset($this->items[$alias]['singleton'])) {
			$previousSingletonSetting = $this->items[$alias]['singleton'];
			$this->items[$alias]['singleton'] = false;
		}

		$concrete = $this->singletons[$name] = $this->get($alias, $args);

		// Reset to the previous value
		if (isset($previousSingletonSetting)) {
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

		return $this->isSingleton($alias);
	}
}
