<?php

namespace Icybee\Modules\Images\Routing;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Images\Operation\WidgetOperation;
use Icybee\Routing\RouteMaker as Make;
use ICanBoogie\Routing\RouteDefinition as Route;

return [

	'images:show' => [

		Route::PATTERN => '/images/<uuid:{:uuid:}><extension:[\.a-z]*>',
		Route::CONTROLLER => ImagesController::class,
		Route::ACTION => Make::ACTION_SHOW,
		Route::VIA => Request::METHOD_GET,

	],

	'images:download' => [

		Route::PATTERN => '/images/download/<uuid:{:uuid:}><extension:[\.a-z]*>',
		Route::CONTROLLER => ImagesController::class,
		Route::ACTION => 'download',
		Route::VIA => Request::METHOD_GET,

	],

	'images:protected:show' => [

		Route::PATTERN => '/images/<nid:\d+><extension:[\.a-z]*>',
		Route::CONTROLLER => ImagesAdminController::class,
		Route::ACTION => 'show',

	],

	'images:protected:download' => [

		Route::PATTERN => '/images/download/<nid:\d+><extension:[\.a-z]*>',
		Route::CONTROLLER => ImagesAdminController::class,
		Route::ACTION => 'download',

	],

	'images/thumbnail' => [

		Route::PATTERN => '/images/<uuid:{:uuid:}>/<size:\d+x\d+|\d+x|x\d+>*',
		Route::CONTROLLER => ThumbnailController::class,
		Route::VIA => Request::METHOD_GET,

	],

	'images/thumbnail-version' => [

		Route::PATTERN => '/images/<uuid:{:uuid:}>/thumbnails/:version',
		Route::CONTROLLER => ThumbnailController::class,
		Route::VIA => Request::METHOD_GET,

	],

	'protected:images/thumbnail' => [

		Route::PATTERN => '/images/<nid:\d+>/<size:\d+x\d+|\d+x|x\d+>*',
		Route::CONTROLLER => ThumbnailController::class,
		Route::VIA => Request::METHOD_GET,

	],

	'protected:images/thumbnail-version' => [

		Route::PATTERN => '/images/<nid:\d+>/thumbnails/:version',
		Route::CONTROLLER => ThumbnailController::class,
		Route::VIA => Request::METHOD_GET,

	],

	/*
	 * api
	 */

	'api:widgets:adjust-image:popup' => [

		Route::PATTERN => '/api/widgets/adjust-image/:mode',
		Route::CONTROLLER => WidgetOperation::class,
		Route::VIA => Request::METHOD_GET,

	],

	'api:widgets:adjust-thumbnail:popup' => [

		Route::PATTERN => '/api/widgets/adjust-thumbnail/:mode',
		Route::CONTROLLER => WidgetOperation::class,
		Route::VIA => Request::METHOD_GET,

	],

	/*
	 * admin
	 */

	'redirect:admin/resources' => [

		Route::PATTERN => '/admin/resources',
		Route::LOCATION => '/admin/images',

	]

] + Make::admin('images', ImagesAdminController::class, [

	Make::OPTION_ID_NAME => 'nid',
	Make::OPTION_ACTIONS => [

		'gallery' => [ '/{name}/gallery', Request::METHOD_ANY ]

	]

]);
