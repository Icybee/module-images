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

use Brickrouge\Document;

use Icybee\Modules\Files\ManageBlock as FilesManageBlock;

class ManageBlock extends FilesManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/slimbox.js');
		$document->css->add(DIR . 'public/slimbox.css');
		$document->js->add('manage.js');
	}

	public function __construct(Module $module, array $attributes)
	{
		parent::__construct($module, $attributes + [

			self::T_COLUMNS_ORDER => [

				'title', 'size', 'download', 'is_online', 'uid', 'surface', 'updated_at'

			]
		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `surface`: An instance of {@link ManageBlock\SurfaceColumn}.
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			'title' => ManageBlock\TitleColumn::class,
			'surface' => ManageBlock\SurfaceColumn::class

		]);
	}
}

namespace Icybee\Modules\Images\ManageBlock;

use ICanBoogie\ActiveRecord\Query;

use Icybee\ManageBlock;
use Icybee\ManageBlock\Column;
use Icybee\Modules\Images\Image;
use Icybee\Modules\Images\ThumbnailDecorator;
use Icybee\Modules\Nodes\ManageBlock\TitleColumn as IcybeeTitleColumn;

/**
 * Class for the `title` column.
 */
class TitleColumn extends IcybeeTitleColumn
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

/**
 * Class for the `surface` column.
 *
 * @todo-20130624: disable options, when count < 10
 */
class SurfaceColumn extends Column
{
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
	 * Adds support for the `surface` filter.
	 *
	 * @inheritdoc
	 */
	public function alter_filters(array $filters, array $modifiers)
	{
		$filters = parent::alter_filters($filters, $modifiers);

		if (isset($modifiers['surface']))
		{
			$value = $modifiers['surface'];

			if (in_array($value, [ 'l', 'm', 's' ]))
			{
				$filters['surface'] = $value;
			}
			else
			{
				unset($filters['surface']);
			}
		}

		return $filters;
	}

	/**
	 * Adds support for the `surface` filter.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_filter(Query $query, $filter_value)
	{
		if ($filter_value)
		{
			list($avg, $max, $min) = $query->model
				->select('AVG(width * height), MAX(width * height), MIN(width * height)')
				->similar_site
				->one(\PDO::FETCH_NUM);

			$bounds = [

				$min,
				round($avg - ($avg - $min) / 3),
				round($avg),
				round($avg + ($max - $avg) / 3),
				$max

			];

			switch ($filter_value)
			{
				case 'l': $query->where('width * height >= ?', $bounds[3]); break;
				case 'm': $query->where('width * height >= ? AND width * height < ?', $bounds[2], $bounds[3]); break;
				case 's': $query->where('width * height < ?', $bounds[2]); break;
			}
		}

		return $query;
	}

	/**
	 * Alters the order of the query with the surface of the image.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		return $query->order('(width * height) ' . ($order_direction < 0 ? 'DESC' : 'ASC'));
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
