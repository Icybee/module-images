<?php

namespace Icybee\Modules\Images\Operation;

use ICanBoogie\HTTP\Request;

use Icybee\Modules\Images\AdjustImage;

class WidgetOperation extends \Icybee\Operation\Widget\Get
{
	protected function get_widget_class()
	{
		return AdjustImage::class;
	}

	public function action(Request $request)
	{
		$request['class'] = 'adjust-image';

		return parent::action($request);
	}
}
