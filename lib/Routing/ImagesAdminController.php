<?php

namespace Icybee\Modules\Images\Routing;

use Icybee\Modules\Files\Routing\FilesAdminController;
use Icybee\Modules\Images\AdjustImage;

class ImagesAdminController extends FilesAdminController
{
	protected function action_gallery()
	{
		$this->view->content = $this->module->getBlock('gallery')
			->add_class('block--manage'); // not really on the right target :(
		$this->view['block_name'] = 'gallery';
	}

	protected function widget_adjust_image_with_popup()
	{
		return new AdjustImage([ 'value' => $this->request['selected'] ]);
	}
}
