<?php

include './vendor/autoload.php';

use Fuel\Dependency\ServiceProvider;

foreach (glob(__DIR__.'/stubs/*.php') as $stub)
{
	require $stub;
}