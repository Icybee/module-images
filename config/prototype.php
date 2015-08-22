<?php

namespace Icybee\Modules\Images;

$hooks = Hooks::class . '::';

return [

	'Icybee\Modules\Nodes\Node::lazy_get_image' => $hooks . 'prototype_get_image',
	'Icybee\Modules\Images\Image::thumbnail' => $hooks . 'prototype_thumbnail',
	'Icybee\Modules\Images\Image::lazy_get_thumbnail' => $hooks . 'prototype_get_thumbnail'

];
