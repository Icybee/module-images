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

		$document->js->add(DIR . 'public/module.js');
		$document->css->add(DIR . 'public/module.css');
	}

	public function __construct($attributes=array())
	{
		parent::__construct
		(
			$attributes + array
			(
				Element::IS => 'PopImage',

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
				'thumbnail-version' => $this[self::THUMBNAIL_VERSION]
			)
		);
	}

	protected function getEntry($model, $value)
	{
		return $model->where('path = ? OR title = ? OR slug = ?', $value, $value, $value)->order('created_at DESC')->one;
	}

	protected function getPreview($record)
	{
		if (!$record)
		{
			return new Element('img');
		}

		return $record->thumbnail($this[self::THUMBNAIL_VERSION])->to_element(array(

			'data-nid' => $record->nid,
			'data-path' => $record->url('get')

		));
	}
}