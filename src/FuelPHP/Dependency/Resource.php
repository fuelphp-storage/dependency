<?php

namespace FuelPHP\Dependency;

use Closure;
use ReflectionClass;
use ReflectionParameter;

class Resource
{
	public $translation;

	public $preferSingleton = false;

	public function __construct($translation)
	{
		$this->translation = $translation;
	}

	public function preferSingleton($prefer = true)
	{
		$this->preferSingleton = $prefer;

		return $this;
	}

	public function resolve(Container $container, array $arguments = array())
	{
		if ($this->translation instanceof Closure) {
			$callback = $this->translation;

			return $callback($container, $arguments);
		}

		if (is_string($this->translation) and class_exists($this->translation)) {
			return $this->create($container, $arguments);
		}

		return $this->translation;
	}

	protected function create(Container $container, array $arguments = array())
	{
		$class = new ReflectionClass($this->translation);

		// Raise an error when the class is not instantiatable.
		if ( ! $class->isInstantiable()) {
			throw new ResolveException('Class '.$this->translation.' is not instantiable.');
		}

		// Return a new instance when there is no constructor
		if ( ! $constructor = $class->getConstructor()) {
			return new $this->translation;
		}

		// Retrieve the constructor arguments
		$parameters = $constructor->getParameters();

		// Remove the parameters which are supplied
		$parameters = array_slice($parameters, count($arguments));

		// Resolve the remaining parameters;
		$parameters = $this->resolveParameters($container, $parameters);

		// return a new instance with arguments.
		return $class->newInstanceArgs(array_merge($arguments, $parameters));
	}

	protected function resolveParameters(Container $container, $parameters)
	{
		foreach ($parameters as $index => $parameter) {
			$parameters[$index] = $this->resolveParameter($container, $parameter);
		}

		return $parameters;
	}

	protected function resolveParameter(Container $container, ReflectionParameter $parameter)
	{
		if ($class = $parameter->getClass()) {
			try {
				return $container->resolve($class->name);
			} catch (ResolveException $e) {
				// Let this one pass, fall back to default value
			}
		}

		if ($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}

		if (isset($e)) {
			throw $e;
		}

		throw new ResolveException('Could not resolve parameter '.$parameter->name.' for class '.$this->translation);
	}
}