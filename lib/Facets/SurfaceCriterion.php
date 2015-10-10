<?php

namespace Icybee\Modules\Images\Facets;

use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\Facets\Criterion;

class SurfaceCriterion extends Criterion
{
	/**
	 * Adds support for the `surface` filter.
	 *
	 * @inheritdoc
	 */
	public function alter_conditions(array &$conditions, array $modifiers)
	{
		parent::alter_conditions($conditions, $modifiers);

		if (isset($modifiers['surface']))
		{
			$value = $modifiers['surface'];

			if (in_array($value, [ 'l', 'm', 's' ]))
			{
				$conditions['surface'] = $value;
			}
			else
			{
				unset($conditions['surface']);
			}
		}
	}

	/**
	 * Adds support for the `surface` filter.
	 *
	 * @inheritdoc
	 */
	public function alter_query_with_value(Query $query, $value)
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

		switch ($value)
		{
			case 'l': $query->and('width * height >= ?', $bounds[3]); break;
			case 'm': $query->and('width * height >= ? AND width * height < ?', $bounds[2], $bounds[3]); break;
			case 's': $query->and('width * height < ?', $bounds[2]); break;
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
}
