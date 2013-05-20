<?php

include './vendor/autoload.php';

use FuelPHP\Dependency\ServiceProvider;

foreach (glob(__DIR__.'/stubs/*.php') as $stub)
{
	require $stub;
}