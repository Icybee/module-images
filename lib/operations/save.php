<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images;

class SaveOperation extends \Icybee\Modules\Files\SaveOperation
{
	protected $accept = [ '.gif', '.png', '.jpg', '.jpeg' ];

	protected function control(array $controls)
	{
		unset($request[Image::WIDTH]);
		unset($request[Image::HEIGHT]);

		return parent::control($controls);
	}

	protected function alter_request_with_file(Request $request, \ICanBoogie\HTTP\File $file)
	{
		list($w, $h) = getimagesize($file->pathname);

		$request[Image::WIDTH] = $w;
		$request[Image::HEIGHT] = $h;
	}
}