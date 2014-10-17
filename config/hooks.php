<?php

namespace Icybee\Modules\Images;

$hooks = __NAMESPACE__ . '\Hooks::';

return array
(
	'events' => array
	(
		'Icybee\Modules\Nodes\SaveOperation::process' => $hooks . 'on_nodes_save',
		'Icybee\Modules\Contents\ConfigBlock::alter_children' => $hooks . 'on_contents_configblock_alter_children',
		'Icybee\Modules\Contents\ConfigOperation::process:before' => $hooks . 'before_contents_config',
		'Icybee\Modules\Contents\Content::alter_css_class_names' => $hooks . 'on_alter_css_class_names',
		'Icybee\Modules\Contents\EditBlock::alter_children' => $hooks . 'on_contents_editblock_alter_children',
		'Icybee\Modules\Contents\View::alter_records' => $hooks . 'on_contents_view_alter_records',
		'Icybee\Modules\Pages\PageRenderer::render' => $hooks . 'on_page_renderer_render',
		'Icybee\Modules\Contents\ManageBlock::alter_rendered_cells' => $hooks . 'on_manageblock_alter_rendered_cells'
	),

	'prototypes' => array
	(
		'Icybee\Modules\Nodes\Node::lazy_get_image' => $hooks . 'prototype_get_image',
		'Icybee\Modules\Images\Image::thumbnail' => $hooks . 'prototype_thumbnail',
		'Icybee\Modules\Images\Image::lazy_get_thumbnail' => $hooks . 'prototype_get_thumbnail'
	),

	'textmark' => array
	(
		'images.reference' => array
		(
			$hooks . 'textmark_images_reference'
		)
	)
);