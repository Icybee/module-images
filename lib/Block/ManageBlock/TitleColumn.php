<?php

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
