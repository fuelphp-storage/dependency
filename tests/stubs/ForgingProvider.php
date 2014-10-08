<?php

use Fuel\Dependency\ServiceProvider;
use Fuel\Dependency\Resource;

class ForgingProvider extends ServiceProvider
{
	public $namespace = 'forging';

	public function provide() {}

	public function forge($name, array $arguments = [])
	{
		if ( ! empty($arguments))
		{
			$resource = new Resource('stdClass');
			$resource->preferSingleton();

			return $resource;
		}

		return 'stdClass';
	}
}
