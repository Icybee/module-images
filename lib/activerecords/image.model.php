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

class Model extends \Icybee\Modules\Files\Model
{
	static protected $accept = [ '.gif', '.png', '.jpg', '.jpeg' ];

	public function save(array $properties, $key=null, array $options=[])
	{
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
	public function including_assigned_image(array $records)
	{
		$keys = array();

		foreach ($records as $record)
		{
			$keys[] = $record->nid;
		}

		if (!$keys)
		{
			return $records;
		}

		$pairs = ActiveRecord\get_model('registry/node')
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
			$image_key = $pairs[$nid];

			if (empty($images[$image_key]))
			{
				continue;
			}

			$record->image = $images[$image_key];
		}

		return $records;
	}
}