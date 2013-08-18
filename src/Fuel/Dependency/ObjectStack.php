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

use SplStack;
use Countable;

/**
 *
 */
class ObjectStack implements Countable
{
	/**
	 * @var
	 */
	protected $stack;

	/**
	 * @var
	 */
	protected $container;

	/**
	 * @var
	 */
	protected $identifier;

	/**
	 * Constructor
	 *
	 * @param   Fuel\Depedency\Container  $container   container
	 * @param   string                    $identifier  identifier
	 */
	public function __construct(Container $container, $identifier)
	{
		$this->stack = new SplStack;
		$this->container = $container;
		$this->identifier = $identifier;
	}

	/**
	 * Create a new instance and pushes it on the stack.
	 *
	 * @param   array  $arguments  constructor arguments
	 * @return  object  resolved dependency
	 */
	public function push(array $arguments = array())
	{
		$instance = $this->container->resolve($this->identifier, $arguments);
		$this->stack->push($instance);

		return $instance;
	}

	/**
	 * Pop a instance off the stack and return it
	 *
	 * @return  object  instance
	 */
	public function pop()
	{
		if ( ! $this->stack->isEmpty())
		{
			return $this->stack->pop();
		}
	}

	/**
	 * Get the current/top instance off the stack
	 *
	 * @return  object  instance
	 */
	public function top()
	{
		if ( ! $this->stack->isEmpty())
		{
			return $this->stack->top();
		}
	}

	/**
	 * Get the first/bottom instance off the stack
	 *
	 * @return  object  instance
	 */
	public function bottom()
	{
		if ( ! $this->stack->isEmpty())
		{
			return $this->stack->bottom();
		}
	}

	/**
	 * Get the number of instances on the stack
	 *
	 * @return  int  count
	 */
	public function count()
	{
		return count($this->stack);
	}
}
