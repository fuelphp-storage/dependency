<?php

namespace Fuel\Dependency;

use SplStack;

class Stack
{
	protected $stack;

	protected $container;

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
		try
		{
			return $this->stack->pop();
		}
		catch (\RuntimeException $e) {}
	}

	/**
	 * Get the currect/top instance off the stack
	 *
	 * @return  object  instance
	 */
	public function current()
	{
		if ($this->stack->isEmpty())
		{
			return $this->push();
		}

		return $this->stack->top();
	}
}