<?php

namespace Icybee\Modules\Images\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Query;

use Icybee\Block\ManageBlock;
use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Images\Image;

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
