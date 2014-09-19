<?php

interface ConstructorFailDep
{

}

class ConstructorFail
{
	public function __construct(ConstructorFailDep $dep)
	{
	}
}