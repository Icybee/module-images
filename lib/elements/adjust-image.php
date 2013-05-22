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
	public function __construct(array $attributes=array())
	{
		parent::__construct
		(
			$attributes + array
			(
				self::T_CONSTRUCTOR => 'images',

				'data-adjust' => 'adjust-image'
			)
		);
	}

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add('adjust-image.css');
		$document->js->add('adjust-image.js');
	}

	protected function get_records($constructor, array $options, $limit=16)
	{
		return parent::get_records($constructor, $options, $limit);
	}

	protected function render_record(\Icybee\Modules\Nodes\Node $record, $selected, array $range, array $options)
	{
		$recordid = $record->nid;

		return new Element
		(
			'a', array
			(
				Element::CHILDREN => array
				(
					$record->thumbnail('$icon-m')->to_element
					(
						array
						(
							'alt' => $record->alt,
							'title' => $record->title,

							'data-nid' => $recordid,
							'data-popover-image' => $record->thumbnail('$popover')->url,
							'data-popover-target' => '.widget-adjust-image'
						)
					)
				),

				'href' => '#',

				'data-nid' => $recordid,
				'data-title' => $record->title,
				'data-path' => $record->path
			)
		);
	}

	public function get_results(array $options=array(), $constructor='images')
	{
		return parent::get_results($options, $constructor);
	}
}

namespace Brickrouge\Widget;

class AdjustImage extends \Icybee\Modules\Images\AdjustImage
{

}