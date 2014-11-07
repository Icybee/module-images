<?php

namespace Icybee\Modules\Images;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

return [

	/*
	 * api
	 */

	'api:images/thumbnail' => [

		'pattern' => '/api/images/<nid:\d+>/<size:\d+x\d+|\d+x|x\d+>*',
		'controller' => __NAMESPACE__ . '\ThumbnailOperation',
		'via' => Request::METHOD_GET

	],

	'api:images/thumbnail-version' => [

		'pattern' => '/api/images/<nid:\d+>/thumbnails/:version',
		'controller' => __NAMESPACE__ . '\ThumbnailOperation',
		'via' => Request::METHOD_GET

	],

	/*
	 * admin
	 */

	'admin:images/gallery' => [

		'pattern' => '/admin/images/gallery',
		'controller' => __NAMESPACE__ . '\GalleryController',
		'title' => '.gallery',
		'block' => 'gallery'

	],

	'!admin:config' => [

		'pattern' => '!auto',
		'controller' => true

	],

	'redirect:admin/resources' => [

		'pattern' => '/admin/resources',
		'location' => '/admin/images'

	]
];
