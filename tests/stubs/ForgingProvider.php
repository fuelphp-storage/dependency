<?php

use Fuel\Dependency\ServiceProvider;
use Fuel\Dependency\Resource;

class ForgingProvider extends ServiceProvider
{
	public $namespace = 'forging';

	public function provide() {}

	public function forge($name, array $arguments = array())
	{
		if ( ! empty($arguments))
		{
			return $this->singleton(new Resource('stdClass'));
		}

		return 'stdClass';
	}
}
