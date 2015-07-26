<?php

namespace Icybee\Modules\Images\Routing;

use Icybee\Modules\Files\Routing\FilesAdminController;

class ImagesAdminController extends FilesAdminController
{
	protected function action_gallery()
	{
		$this->view->content = $this->module->getBlock('gallery')
			->add_class('block--manage'); // not really on the right target :(
		$this->view['block_name'] = 'gallery';
	}
}
