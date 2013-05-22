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

class PopImage extends \Icybee\Modules\Nodes\PopNode
{
	const THUMBNAIL_VERSION = '#popimage-thumbnail-version';

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add('pop-image.css');
		$document->js->add('pop-image.js');
	}

	public function __construct($attributes=array())
	{
		parent::__construct
		(
			$attributes + array
			(
				self::T_CONSTRUCTOR => 'images',
				self::THUMBNAIL_VERSION => '$popimage',

				'placeholder' => 'Sélectionner une image',

				'data-adjust' => 'adjust-image'
			)
		);
	}

	protected function alter_dataset(array $dataset)
	{
		return parent::alter_dataset
		(
			$dataset + array
			(
				'widget-constructor' => 'PopImage',
				'thumbnail-version' => $this[self::THUMBNAIL_VERSION]
			)
		);
	}

	protected function getEntry($model, $value)
	{
		return $model->where('path = ? OR title = ? OR slug = ?', $value, $value, $value)->order('created DESC')->one;
	}

	protected function getPreview($record)
	{
		return new Element
		(
			'img', array
			(
				'src' => $record ? $record->thumbnail($this[self::THUMBNAIL_VERSION])->url : null,
				'alt' => ''
			)
		);
	}
}