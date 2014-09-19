<?php
// Here you can initialize variables that will be available to your tests

require __DIR__.'/../../vendor/autoload.php';

$files = glob(__DIR__.'/../stubs/*.php');

foreach ($files as $file)
{
	require_once realpath($file);
}
