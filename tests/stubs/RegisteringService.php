<?php

use Fuel\Dependency\ServiceProvider;

class RegisteringService extends ServiceProvider
{
	public $provides = true;

	public function provide()
	{
		$this->registerSingleton('forge', function($container) {
			return (object) compact('container', 'arguments');
		});

		$this->extend('forge', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});

		$forge = $this->forge('forge');

		$this->registerSingleton('resolve', function($container) {
			return (object) compact('container', 'arguments');
		});

		$resolve = $this->resolve('resolve');

		$this->registerSingleton('resolveSingleton', function($container) {
			return (object) compact('container', 'arguments');
		});

		$this->extendMultiton('resolveSingleton', '__default__', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});

		$resolveSingleton = $this->multiton('resolveSingleton');

		$this->register('from.service', function($container) use ($forge, $resolve, $resolveSingleton) {
			return (object) compact('forge', 'resolve', 'resolveSingleton');
		});
	}
}
