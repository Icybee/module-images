<?php

namespace Icybee\Modules\Images;

use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Nodes\SaveOperation::class . '::process' => $hooks . 'on_nodes_save',
	Icybee\Modules\Contents\ConfigBlock::class . '::alter_children' => $hooks . 'on_contents_configblock_alter_children',
	Icybee\Modules\Contents\ConfigOperation::class . '::process:before' => $hooks . 'before_contents_config',
	Icybee\Modules\Contents\Content::class . '::alter_css_class_names' => $hooks . 'on_alter_css_class_names',
	Icybee\Modules\Contents\EditBlock::class . '::alter_children' => $hooks . 'on_contents_editblock_alter_children',
	Icybee\Modules\Contents\View::class . '::alter_records' => $hooks . 'on_contents_view_alter_records',
	Icybee\Modules\Pages\PageRenderer::class . '::render' => $hooks . 'on_page_renderer_render',
	Icybee\Modules\Contents\ManageBlock::class . '::alter_rendered_cells' => $hooks . 'on_manageblock_alter_rendered_cells'

];
