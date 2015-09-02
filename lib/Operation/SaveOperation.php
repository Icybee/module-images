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

	protected function lazy_get_properties()
	{
		$properties = parent::lazy_get_properties();

		unset($properties[Image::WIDTH]);
		unset($properties[Image::HEIGHT]);

		return $properties;
	}
}
