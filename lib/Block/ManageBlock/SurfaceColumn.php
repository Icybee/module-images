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
use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Images\Image;

/**
 * Class for the `surface` column.
 */
class SurfaceColumn extends Column
{
	use ManageBlock\CriterionColumnTrait;

	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'class' => 'pull-right measure',
			'orderable' => true,
			'filters' => [

				'options' => [

					'=l' => 'Large',
					'=m' => 'Medium',
					'=s' => 'Small'
				]
			]
		]);
	}

	/**
	 * @param Image $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return "{$record->width}&times;{$record->height}&nbsp;px";
	}
}
