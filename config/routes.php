<?php

namespace Icybee\Modules\Images;

return array
(
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