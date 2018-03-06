<?php namespace Nickwest\EloquentForms;

class DefaultTheme extends Theme
{
	public function view_namespace() : string
	{
		return 'form-maker';
	}
}
