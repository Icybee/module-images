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

			self::T_CONSTRUCTOR => 'images',
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

	protected function render_record(\Icybee\Modules\Nodes\Node $record, $selected, array $range, array $options)
	{
		/* @var $record Image */

		$nid = $record->nid;

		return $record->thumbnail('$icon-m')->to_element([

			'alt' => $record->alt,
			'title' => $record->title,

			'data-nid' => $nid,
			'data-popover-image' => $record->thumbnail('$popover')->url,
			'data-popover-target' => '.widget-adjust-image',
			'data-title' => $record->title,
			'data-path' => $this->app->url_for('api:images/compat-get', $record)

		]);
	}

	/**
	 * Defaults constructor to `images`.
	 *
	 * @inheritdoc
	 */
	public function get_results(array $options = [], $constructor = 'images')
	{
		return parent::get_results($options, $constructor);
	}
}
