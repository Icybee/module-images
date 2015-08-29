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

use Icybee\Modules\Images\Image;

class SaveOperation extends \Icybee\Modules\Files\Operation\SaveOperation
{
	protected $accept = [ '.gif', '.png', '.jpg', '.jpeg' ];

	protected function control(array $controls)
	{
		$request = $this->request;

		unset($request[Image::WIDTH]);
		unset($request[Image::HEIGHT]);

		return parent::control($controls);
	}
}
