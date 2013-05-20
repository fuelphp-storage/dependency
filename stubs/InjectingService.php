<?php

use FuelPHP\Dependency\ServiceProvider;

class InjectingService extends ServiceProvider
{
	public $provides = array('service');

	public function provide()
	{
		$this->inject('service', $this);
	}
}