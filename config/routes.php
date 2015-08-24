<?php

namespace Icybee\Modules\Images;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;
use Icybee\Routing\RouteMaker as Make;

return [

	/*
	 * api
	 */

	'api:images/thumbnail' => [

		'pattern' => '/api/images/<nid:\d+>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:images/thumbnail-version' => [

		'pattern' => '/api/images/<nid:\d+>/thumbnails/:version',
		'controller' => ThumbnailOperation::class,
		'via' => Request::METHOD_GET

	],

	'api:images/compat-get' => [

		'pattern' => '/api/images/<nid:\d+>',
		'controller' => CompatShowOperation::class,
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
