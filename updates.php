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

use ICanBoogie\Updater\Update;
use ICanBoogie\Updater\AssertionFailed;

/**
 * @module images
 */
class Update20130426 extends Update
{
	/**
	 * Rename the node meta `resources_images.imageid` as `image_id`.
	 */
	public function update_node_meta()
	{
		$model = $this->app->models['registry/node'];
		$count = $model->filter_by_name("resources_images.imageid")->count;

		if (!$count)
		{
			throw new AssertionFailed(__FUNCTION__, [ "resources_images.imageid" ]);
		}

		$model('UPDATE {self} SET name = "image_id" WHERE name = "resources_images.imageid"');
	}

	public function update_registry()
	{
		$model = $this->app->models['registry'];
		$count = $model->where('name LIKE "%resources_images%"')->count;

		if (!$count)
		{
			throw new AssertionFailed(__FUNCTION__, [ "resources_images" ]);
		}

		$model('UPDATE {self} SET name = REPLACE(name, "resources_images", "images") WHERE name LIKE "%resources_images%"');
	}
}