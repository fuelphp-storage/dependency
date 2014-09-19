<?php

class DependedOn
{

}

class Depending
{
	public $dep;
	public function __construct(DependedOn $dependency)
	{
		$this->dep = $dependency;
	}
}