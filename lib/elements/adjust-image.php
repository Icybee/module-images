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

use Brickrouge\Element;

class AdjustImage extends \Brickrouge\Widget\AdjustNode
{
	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes=array())
	{
		parent::__construct
		(
			$attributes + array
			(
				self::T_CONSTRUCTOR => 'images',
				self::WIDGET_CONSTRUCTOR => 'AdjustImage',

				'data-adjust' => 'adjust-image'
			)
		);
	}

	/**
	 * Adds the `widget-adjust-image` class name.
	 */
	protected function alter_class_names(array $class_names)
	{
		return parent::alter_class_names($class_names) + array
		(
			'widget-adjust-image' => true
		);
	}

	/**
	 * Because a 4x4 grid is used, `$limit` defaults to 16.
	 */
	protected function get_records($constructor, array $options, $limit=16)
	{
		return parent::get_records($constructor, $options, $limit);
	}

	protected function render_record(\Icybee\Modules\Nodes\Node $record, $selected, array $range, array $options)
	{
		$nid = $record->nid;

		return $record->thumbnail('$icon-m')->to_element
		(
			array
			(
				'alt' => $record->alt,
				'title' => $record->title,

				'data-nid' => $nid,
				'data-popover-image' => $record->thumbnail('$popover')->url,
				'data-popover-target' => '.widget-adjust-image',
				'data-title' => $record->title,
				'data-path' => $record->path
			)
		);
	}

	/**
	 * Defaults constructor to `images`.
	 */
	public function get_results(array $options=array(), $constructor='images')
	{
		return parent::get_results($options, $constructor);
	}
}

namespace Brickrouge\Widget;

class AdjustImage extends \Icybee\Modules\Images\AdjustImage
{

}