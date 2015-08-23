<?php

namespace Icybee\Modules\Images;

use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Nodes\Node::class . '::lazy_get_image' => $hooks . 'prototype_get_image',
	Icybee\Modules\Images\Image::class . '::thumbnail' => $hooks . 'prototype_thumbnail',
	Icybee\Modules\Images\Image::class . '::lazy_get_thumbnail' => $hooks . 'prototype_get_thumbnail'

];
