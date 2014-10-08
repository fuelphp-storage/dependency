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

/**
 * Creates a context for each resolve
 */
class ResolveContext
{
	use ContainerAware;

	/**
	 * Name of instance
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Checks whether this is a multiton resolve
	 *
	 * @var boolean
	 */
	protected $multiton = false;

	function __construct(Container $container, $name = null, $multiton = false)
	{
		$this->container = $container;
		$this->name = $name;
		$this->multiton = $multiton;
	}

	/**
	 * Returns the name of instance
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Checks whether the instance is a multiton
	 *
	 * @return boolean
	 */
	public function isMultiton()
	{
		return $this->multiton;
	}
}
