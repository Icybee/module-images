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
use Brickrouge\Element;

use Icybee\Modules\Nodes\AdjustNode;
use Icybee\Modules\Nodes\Node;

class AdjustImage extends AdjustNode
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes + [

			self::T_CONSTRUCTOR => Image::MODEL_ID,
			Element::IS => 'AdjustImage',

			'data-adjust' => 'adjust-image'

		]);
	}

	/**
	 * Adds the `widget-adjust-image` class name.
	 *
	 * @inheritdoc
	 */
	protected function alter_class_names(array $class_names)
	{
		return parent::alter_class_names($class_names) + [

			'widget-adjust-image' => true

		];
	}

	/**
	 * Because a 4x4 grid is used, `$limit` defaults to 16.
	 *
	 * @inheritdoc
	 */
	protected function get_records($constructor, array $options, $limit = 16)
	{
		return parent::get_records($constructor, $options, $limit);
	}

	/**
	 * @param Node|Image $record
	 * @param mixed $selected
	 * @param array $range
	 * @param array $options
	 *
	 * @return Element
	 */
	protected function render_record(Node $record, $selected, array $range, array $options)
	{
		return $record->thumbnail('$icon-m')->to_element([

			'alt' => null,
			'title' => null,

			'data-nid' => $record->nid,
			'data-uuid' => $record->uuid,
			'data-popover-image' => $record->thumbnail('$popover')->url,
			'data-popover-target' => '.widget-adjust-image',
			'data-popover-title' => $record->title,
			'data-title' => $record->title,
			'data-path' => $this->app->url_for('images:show', $record)

		]);
	}

	/**
	 * Defaults constructor to {@link Image::MODEL_ID}.
	 *
	 * @inheritdoc
	 */
	public function get_results(array $options = [], $constructor = Image::MODEL_ID)
	{
		return parent::get_results($options, $constructor);
	}
}
