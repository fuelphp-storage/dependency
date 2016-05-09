# Fuel Dependency

[![Build Status](https://img.shields.io/travis/fuelphp/dependency.svg?style=flat-square)](https://travis-ci.org/fuelphp/dependency)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/fuelphp/dependency.svg?style=flat-square)](https://scrutinizer-ci.com/g/fuelphp/dependency)
[![Quality Score](https://img.shields.io/scrutinizer/g/fuelphp/dependency.svg?style=flat-square)](https://scrutinizer-ci.com/g/fuelphp/dependency)

**Fuel Dependency package based on [League\Container](http://container.thephpleague.com).**


## Usage

The Dependency package is an extension of [League\Container](http://container.thephpleague.com) responsible for handling dependencies in FuelPHP framework. Most of the functionalities are the same, but there are some custom ones as well. It means that we don't encourage anyone to use this package on it's own since the additions are mostly FuelPHP specific.

This documentation covers the basic usage of the Container, also the added features. For full documentation check the original [documentation](http://container.thephpleague.com).


### Container

The container is the primary component of the dependency package and ties all the parts together. You can look at this as the (PHP) object store. The container is where you register resources, service providers and retrieve dependencies.


``` php
$container = new Fuel\Dependency\Container;
```


### Definitions

A definition is either a class string name or a closure which returns an instance or a class name.

#### String definition

``` php
// Register
$container->add('string', 'stdClass');

// Resolve
$instance = $container->get('string');
```

#### Closure definition

``` php
// Register
$container->add('closure.object', function() {
	return new stdClass;
});

// Resolve
$instance = $container->get('closure.object');
```


### Service Providers

Service providers are used to expose packages to the Container. A Service
Provider can provide the container with resources but also act on a namespace.
A namespace is a string prefix which maps identifiers to the providers factory method.

``` php
use League\Container\ServiceProvider;

class MyProvider extends ServiceProvider
{
	protected $provides = ['some.identifier', 'other.resource'];

	public function register()
	{
		$this->container->add('some.identifier', 'stdClass');
		$this->container->singleton('other.resource', function() {
			return new Something($this->container->resolve('database.connection'));
		));
	}
}
```


### Fuel extensions

Fuel adds two main functionalities to the Container:

- Creating multiton instances
- Creating new instances regardless it is singleton or not

#### Multiton

``` php
// Register
$container->add('closure::object1', function() {
	return new stdClass;
});
$container->add('closure::object2', function() {
	return new stdClass;
});

// Resolve
object1 = $container->multiton('closure', 'object1');
objects = $container->multiton('closure');
```

#### Forge

``` php
// Register
$container->singleton('closure.object', function() {
	return new stdClass;
});

// Resolve
// Always returns a newly resolved definition
$instance = $container->forge('closure.object');
```


## Contributing

Thank you for considering contribution to FuelPHP framework. Please see [CONTRIBUTING](https://github.com/fuelphp/fuelphp/blob/master/CONTRIBUTING.md) for details.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
