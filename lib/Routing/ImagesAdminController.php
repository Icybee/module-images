<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images\Routing;

use Icybee\Modules\Files\Routing\FilesAdminController;
use Icybee\Modules\Images\AdjustImage;
use Icybee\Modules\Images\Element\UploadForm;

class ImagesAdminController extends FilesAdminController
{
	protected function action_gallery()
	{
		$this->view->content = $this->module->getBlock('gallery')
			->add_class('block--manage'); // not really on the right target :(
		$this->view['block_name'] = 'gallery';
	}

	protected function action_get_upload()
	{
		$this->view->content = (string) new UploadForm;
	}

	protected function widget_adjust_image_with_popup()
	{
		return new AdjustImage([ 'value' => $this->request['selected'] ]);
	}
}
