<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images\Block\ManageBlock;

use Icybee\Block\ManageBlock;
use Icybee\Modules\Images\Image;
use Icybee\Modules\Images\ThumbnailDecorator;

/**
 * Class for the `title` column.
 */
class TitleColumn extends \Icybee\Modules\Nodes\Block\ManageBlock\TitleColumn
{
	/**
	 * @param Image $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new ThumbnailDecorator(parent::render_cell($record), $record);
	}
}
