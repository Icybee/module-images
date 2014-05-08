<?php

namespace Icybee\Modules\Images;

use ICanBoogie\HTTP\Request;

return array
(
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

	'admin:images/gallery' => array
	(
		'pattern' => '/admin/images/gallery',
		'controller' => __NAMESPACE__ . '\GalleryController',
		'title' => '.gallery',
		'block' => 'gallery'
	),

	'!admin:config' => array
	(

	),

	'redirect:admin/resources' => array
	(
		'pattern' => '/admin/resources',
		'location' => '/admin/images'
	)
);