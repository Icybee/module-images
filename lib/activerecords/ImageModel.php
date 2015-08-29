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

use ICanBoogie\ActiveRecord;
use ICanBoogie\ActiveRecord\RecordNotFound;

use Icybee\Modules\Files\FileModel;

class ImageModel extends FileModel
{
	static protected $accept = [ '.gif', '.png', '.jpg', '.jpeg' ];

	public function save(array $properties, $key = null, array $options = [])
	{
		if (isset($properties[Image::HTTP_FILE]))
		{
			$file = $properties[Image::HTTP_FILE];

			list($w, $h) = getimagesize($file->pathname);

			$properties[Image::WIDTH] = $w;
			$properties[Image::HEIGHT] = $h;
		}

		return parent::save($properties, $key, $options + [

			self::ACCEPT => self::$accept

		]);
	}

	/**
	 * Includes the images assigned to the records.
	 *
	 * The `image` property of the records is set to the associated image.
	 *
	 * @param array $records The records to alter.
	 *
	 * @return array
	 */
	public function including_assigned_image(&$records)
	{
		$keys = [];

		foreach ($records as $record)
		{
			$keys[] = $record->nid;
		}

		if (!$keys)
		{
			return $records;
		}

		$pairs = $this->models['registry/node']
		->select('targetid, value')
		->filter_by_name_and_targetid('image_id', $keys)
		->pairs;

		if (!$pairs)
		{
			return $records;
		}

		try
		{
			$images = $this->find($pairs);
		}
		catch (RecordNotFound $e)
		{
			$images = $e->records;
		}

		foreach ($records as $record)
		{
			$nid = $record->nid;

			if (empty($pairs[$nid]))
			{
				continue;
			}

			$image_key = $pairs[$nid];

			if (empty($images[$image_key]))
			{
				continue;
			}

			$record->image = new NodeRelation($record, $images[$image_key]);
		}

		return $records;
	}
}