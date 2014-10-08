<?php

interface ConstructorDefaultDep
{

}

class ConstructorDefault
{
	public $dep;
	public function __construct(ConstructorDefaultDep $dep = null)
	{
		$this->dep = $dep;
	}
}
