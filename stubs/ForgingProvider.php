<?php

use FuelPHP\Dependency\ServiceProvider;
use FuelPHP\Dependency\Resource;

class ForgingProvider extends ServiceProvider
{
	public $namespace = 'forging';

	public function factory($name, array $arguments = array())
	{
		if ( ! empty($arguments))
		{
			return $this->singleton(new Resource('stdClass'));
		}

		return 'stdClass';
	}
}