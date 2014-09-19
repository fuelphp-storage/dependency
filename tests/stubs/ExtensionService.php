<?php

use Fuel\Dependency\ServiceProvider;

class ExtensionService extends ServiceProvider
{
	public $provides = true;

	public function provide()
	{
		$this->extension('extension', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});
	}
}
