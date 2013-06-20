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
	/**
	 * The following accepted MIME type are returned: image/gif, image/png, image/jpeg.
	 */
	protected function volatile_get_accepted_mime()
	{
		return parent::volatile_get_accepted_mime() + array
		(
			'gif' => 'image/gif',
			'png' => 'image/png',
			'jpg' => 'image/jpeg'
		);
	}

	/**
	 * Sets {@link Image::WIDTH} and {@link Image::HEIGHT} according to the width and height of the
	 * file specified by {@link $file_path}, or unset them if the {link $file_path} is empty.
	 */
	protected function get_properties()
	{
		$properties = parent::get_properties();

		$file_path = $this->file_path;

		if ($file_path)
		{
			list($w, $h) = getimagesize($file_path);

			$properties[Image::WIDTH] = $w;
			$properties[Image::HEIGHT] = $h;
		}
		else
		{
			unset($properties[Image::WIDTH]);
			unset($properties[Image::HEIGHT]);
		}

		return $properties;
	}
}