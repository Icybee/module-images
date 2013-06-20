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
			return;
		}

		$pairs = ActiveRecord\get_model('registry/node')
		->select('targetid, value')
		->filter_by_name_and_targetid('image_id', $keys)
		->pairs;

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