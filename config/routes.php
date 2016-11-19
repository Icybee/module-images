<?php

namespace Icybee\Modules\Images\Routing;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Images\Operation\WidgetOperation;
use Icybee\Routing\RouteMaker as Make;

return [

	'images:show' => [

		'pattern' => '/images/<uuid:{:uuid:}><extension:[\.a-z]*>',
		'controller' => ImagesController::class . '#show',
		'via' => Request::METHOD_GET

	],

	'images:download' => [

		'pattern' => '/images/download/<uuid:{:uuid:}><extension:[\.a-z]*>',
		'controller' => ImagesController::class . '#download',
		'via' => Request::METHOD_GET

	],

	'images:protected:show' => [

		'pattern' => '/images/<nid:\d+><extension:[\.a-z]*>',
		'controller' => ImagesAdminController::class . '#show'

	],

	'images:protected:download' => [

		'pattern' => '/images/download/<nid:\d+><extension:[\.a-z]*>',
		'controller' => ImagesAdminController::class . '#download'

	],

	'images/thumbnail' => [

		'pattern' => '/images/<uuid:{:uuid:}>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => ThumbnailController::class,
		'via' => Request::METHOD_GET

	],

	'images/thumbnail-version' => [

		'pattern' => '/images/<uuid:{:uuid:}>/thumbnails/:version',
		'controller' => ThumbnailController::class,
		'via' => Request::METHOD_GET

	],

	'protected:images/thumbnail' => [

		'pattern' => '/images/<nid:\d+>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => ThumbnailController::class,
		'via' => Request::METHOD_GET

	],

	'protected:images/thumbnail-version' => [

		'pattern' => '/images/<nid:\d+>/thumbnails/:version',
		'controller' => ThumbnailController::class,
		'via' => Request::METHOD_GET

	],

	/*
	 * api
	 */

	'api:widgets:adjust-image:popup' => [

		'pattern' => '/api/widgets/adjust-image/:mode',
		'controller' => WidgetOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:widgets:adjust-thumbnail:popup' => [

		'pattern' => '/api/widgets/adjust-thumbnail/:mode',
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

] + Make::admin('images', ImagesAdminController::class, [

	'id_name' => 'nid',
	'actions' => [

		'gallery' => [ '/{name}/gallery', Request::METHOD_ANY ],
		'upload' => [ '/{name}/upload' , Request::METHOD_GET ],

	]

]);
