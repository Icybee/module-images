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

/**
 * Representation of an image thumbnail.
 */
class Thumbnail extends \ICanBoogie\Modules\Thumbnailer\Thumbnail
{
	/* @var $record \Icybee\Modules\Images\Image */

	protected function get_url()
	{
		$record = $this->src;
		$options = $this->filtered_options;
		$version_name = $this->version_name;

		$url = "/api/images/{$record->nid}";
		$remove_path_params = false;

		if ($version_name)
		{
			$url .= "/thumbnails/" . $version_name;
		}
		else
		{
			$url .= self::format_options_as_path($options);
			$remove_path_params = true;
		}

		$query_string = self::format_options_as_query_string($options, true);

		if ($query_string)
		{
			$url .= '?' . $query_string;
		}

		return $url;
	}

	public function to_element(array $attributes=[])
	{
		$element = parent::to_element($attributes);

		$record = $this->src;

		list($w, $h) = \ICanBoogie\Image::compute_final_size($this->w, $this->h, $this->method, [ $record->width, $record->height ]);

		$element['alt'] = $record->alt;
		$element['width'] = $w;
		$element['height'] = $h;

		return $element;
	}
}