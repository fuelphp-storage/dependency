# FuelPHP - Dependency

This is the FuelPHP Dependency package.

## Contents

The Dependency package is a dependency injection implementation for the FuelPHP framework. In order to provide this functionality the package is responsible for:

* Registering depedencies
* Resolving dependencies
* Storing instances for singletons and multitons.
* Building instances with resolved constructor arguments.

The package resolves dependencies through injected instances, resources and by contacting Service Providers.

## The Container

The container is the primary component of the dependency package and ties all the parts together. You can look at this as the (PHP) object store. The container is where you register resources, service providers and retrieve dependencies.

Create a new `Container`

```
$container = new FuelPHP\Dependency\Container;
```

## Resources

A resource is either a class string name or a closure which returns an instance or a class name.

#### String resource:

```
// Register
$container->register('string', 'stdClass');

// Resolve
$instance = $container->resolve('string');
```

#### Closure resource:

```
// Register
$container->register('closure.object', function() {
	return new stdClass;
});

// Resolve
$instance = $container->resolve('closure.object');
```
or return a string class name

```
// Register
$container->register('closure.string', function() {
	return 'stdClass';
});

// Resolve
$instance = $container->resolve('closure.string');
```

## Extending

Using extensions is a great way to inject additional resources into instances:

```
$container->register('extendable', 'stdClass');

$container->extend('extendable', function($container, $instance)
{
	$instance->extened = true;
});

$instance = $container->resolve('extendable');
$instance->extended;
// > true
```

## Service Providers

Service providers are used to expose packages to the Container. A Service
Provider can provide the container with resources but also act on a namespace.
A namespace is a string prefix which maps identifiers to the providers factory method.

```
&lt;?php
use FuelPHP\Dependency\ServiceProvider;

class MyProvider extends ServiceProvider
{
	public $namespace = 'my';
	public $provides = array('some.identifier', 'other.resource');
	

	public function factory($suffix, array $arguments = array())
	{
		$instance = new Something($suffix);
		$instance->configure($arguments);
		$instance->connection = $this->resolve('database.connection');

		return $instance;
	}
	
	public function provide()
	{
		$this->register('some.identifier', 'stdClass');
		$this->registerSingleton('other.resource', function($container) {
			return new Something($container->resolve('database.connection'));
		));
	}
}
```