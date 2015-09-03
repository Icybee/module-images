<?php

namespace Icybee\Modules\Images;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Images\Operation\CompatShowOperation;
use Icybee\Modules\Images\Operation\ThumbnailOperation;
use Icybee\Modules\Images\Operation\WidgetOperation;
use Icybee\Routing\RouteMaker as Make;

return [

	/*
	 * api
	 */

	'api:images/thumbnail' => [

		'pattern' => '/api/images/<uuid:{:uuid:}>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:images/thumbnail-version' => [

		'pattern' => '/api/images/<uuid:{:uuid:}>/thumbnails/:version',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:protected:images/thumbnail' => [

		'pattern' => '/api/images/<nid:\d+>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:protected:images/thumbnail-version' => [

		'pattern' => '/api/images/<nid:\d+>/thumbnails/:version',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:images/compat-get' => [

		'pattern' => '/api/images/<nid:\d+>',
		'controller' => CompatShowOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:widgets:adjust-image:popup' => [

		'pattern' => '/api/widgets/adjust-image/:mode',
		'controller' => WidgetOperation::class,
		'via' => Request::METHOD_GET

	],

	/*
	 * admin
	 */

	'redirect:admin/resources' => [

		'pattern' => '/admin/resources',
		'location' => '/admin/images'

	]

] + Make::admin('images', Routing\ImagesAdminController::class, [

	'id_name' => 'nid',
	'actions' => [

		'gallery' => [ '/{name}/gallery', Request::METHOD_ANY ]

	]

]);
