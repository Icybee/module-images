<?php

namespace Icybee\Modules\Images\Operation;

use ICanBoogie\HTTP\Request;

use Icybee\Modules\Images\AdjustImage;
use Icybee\Modules\Images\AdjustThumbnail;

class WidgetOperation extends \Icybee\Operation\Widget\Get
{
	protected function get_widget_class()
	{
		return [

			'api:widgets:adjust-image:popup' => AdjustImage::class,
			'api:widgets:adjust-thumbnail:popup' => AdjustThumbnail::class,

		][$this->route->id];
	}

	public function action(Request $request)
	{
		$class = [

			'api:widgets:adjust-image:popup' => 'adjust-image',
			'api:widgets:adjust-thumbnail:popup' => 'adjust-thumbnail'

		][$this->route->id];

		$request['class'] = $class;

		return parent::action($request);
	}
}
