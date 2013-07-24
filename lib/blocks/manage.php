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

class ManageBlock extends \Icybee\Modules\Files\ManageBlock
{
	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/slimbox.js');
		$document->css->add(DIR . 'public/slimbox.css');
		$document->js->add('manage.js');
	}

	public function __construct(Module $module, array $attributes)
	{
		parent::__construct
		(
			$module, $attributes + array
			(
				self::T_COLUMNS_ORDER => array
				(
					'title', 'size', 'download', 'is_online', 'uid', 'surface', 'modified'
				)
			)
		);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `surface`: An instance of {@link ManageBlock\SurfaceColumn}.
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), array
		(
			'title' => __CLASS__ . '\TitleColumn',
			'surface' => __CLASS__ . '\SurfaceColumn'
		));
	}
}

namespace Icybee\Modules\Images\ManageBlock;

use ICanBoogie\ActiveRecord\Query;

use Icybee\Modules\Images\ThumbnailDecorator;

/**
 * Class for the `title` column.
 */
class TitleColumn extends \Icybee\Modules\Nodes\ManageBlock\TitleColumn
{
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
class SurfaceColumn extends \Icybee\ManageBlock\Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'class' => 'pull-right measure',
				'orderable' => true,
				'filters' => array
				(
					'options' => array
					(
						'=l' => 'Large',
						'=m' => 'Medium',
						'=s' => 'Small'
					)
				)
			)
		);
	}

	/**
	 * Adds support for the `surface` filter.
	 */
	public function alter_filters(array $filters, array $modifiers)
	{
		$filters = parent::alter_filters($filters, $modifiers);

		if (isset($modifiers['surface']))
		{
			$value = $modifiers['surface'];

			if (in_array($value, array('l', 'm', 's')))
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
	 */
	public function alter_query_with_filter(Query $query, $filter_value)
	{
		if ($filter_value)
		{
			list($avg, $max, $min) = $query->model->select('AVG(width * height), MAX(width * height), MIN(width * height)')->similar_site->one(\PDO::FETCH_NUM);

			$bounds = array
			(
				$min,
				round($avg - ($avg - $min) / 3),
				round($avg),
				round($avg + ($max - $avg) / 3),
				$max
			);

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
	 */
	public function alter_query_with_order(Query $query, $order_direction)
	{
		return $query->order('(width * height) ' . ($order_direction < 0 ? 'DESC' : 'ASC'));
	}

	public function render_cell($record)
	{
		return "{$record->width}&times;{$record->height}&nbsp;px";
	}
}