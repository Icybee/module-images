<?php

namespace Icybee\Modules\Images;

$hooks = Hooks::class . '::';

return [

	'textmark' => [

		'images.reference' => [

			$hooks . 'textmark_images_reference'

		]
	]
];
