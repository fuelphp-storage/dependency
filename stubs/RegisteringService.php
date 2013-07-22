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

		$forge = $this->forge('forge');

		$this->registerSingleton('resolve', function($container) {
			return (object) compact('container', 'arguments');
		});

		$resolve = $this->resolve('resolve');

		$this->registerSingleton('resolveSingleton', function($container) {
			return (object) compact('container', 'arguments');
		});

		$resolveSingleton = $this->multiton('resolveSingleton');

		$this->register('from.service', function($container, array $arguments) use ($forge, $resolve, $resolveSingleton) {
			return (object) compact('forge', 'resolve', 'resolveSingleton');
		});
	}
}