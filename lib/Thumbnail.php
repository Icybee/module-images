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

use ICanBoogie\Image as ImageSupport;

/**
 * Representation of an image thumbnail.
 */
class Thumbnail extends \ICanBoogie\Modules\Thumbnailer\Thumbnail
{
	protected function get_url()
	{
		/* @var $record Image */

		$record = $this->src;
		$options = $this->filtered_options;
		$version_name = $this->version_name;

		$url = "/images/{$record->uuid}";
		$remove_path_params = false;

		if ($version_name)
		{
			$url .= "/thumbnails/{$version_name}";
		}
		else
		{
			$url .= self::format_options_as_path($options);
			$remove_path_params = true;
		}

		$query_string = self::format_options_as_query_string($options, $remove_path_params);
		$query_string = '?' . $query_string . ($query_string ? '&' : '') . $record->short_hash;

		return $url . $query_string;
	}

	public function to_element(array $attributes = [])
	{
		$element = parent::to_element($attributes);

		$record = $this->src;

		list($w, $h) = ImageSupport::compute_final_size($this->w, $this->h, $this->method, [ $record->width, $record->height ]);

		$element['alt'] = $record->alt;
		$element['width'] = $w;
		$element['height'] = $h;

		return $element;
	}
}
