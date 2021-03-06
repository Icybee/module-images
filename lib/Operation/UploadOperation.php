<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images\Operation;

use ICanBoogie\Modules\Thumbnailer\Thumbnail;

/**
 * Appends a preview to the response of the operation.
 */
class UploadOperation extends \Icybee\Modules\Files\Operation\UploadOperation
{
	protected $accept = [ '.gif', '.png', '.jpg', '.jpeg' ];

	protected function process()
	{
		$rc = parent::process();

		if ($this->response['infos'])
		{
			$path = \ICanBoogie\strip_root($this->file->pathname);

			// TODO-20110106: compute surface w & h and use them for img in order to avoid poping

			$this->response['infos'] = '<div class="preview">'

			.

			new Thumbnail($path, [

				'w' => 64,
				'h' => 64,
				'format' => 'png',
				'background' => 'silver,white,medium',
				'm' => 'surface'

			])

			. '</div>' . $this->response['infos'];
		}

		return $rc;
	}
}
