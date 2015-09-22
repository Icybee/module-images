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

use ICanBoogie\Operation;

use Brickrouge\Document;
use Brickrouge\Element;

class PopOrUploadImage extends Element
{
	const POP_OPTIONS = '#poporuploadimage-pop-options';
	const UPLOAD_OPTIONS = '#poporuploadimage-upload-options';

	private $pop_image;

	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/module.js');
		$document->css->add(DIR . 'public/module.css');
	}

	public function __construct(array $attributes = [])
	{
		$attributes += [

			self::POP_OPTIONS => [],
			self::UPLOAD_OPTIONS => []

		];

		parent::__construct('div', $attributes + [

			Element::CHILDREN => [

				$this->pop_image = new PopImage($attributes[self::POP_OPTIONS]),
				$this->upload_image = new UploadImage($attributes[self::UPLOAD_OPTIONS] + [

					UploadImage::FILE_WITH_LIMIT => true,

					'data-name' => Image::HTTP_FILE,
					'data-upload-url' => Operation::encode('images/save')
				])
			],

			Element::IS => 'PopOrUploadImage'

		]);
	}

	protected function alter_class_names(array $class_names)
	{
		return parent::alter_class_names($class_names) + [

			'widget-class' => 'widget-pop-or-upload-image'

		];
	}

	public function offsetExists($attribute)
	{
		if ($attribute == 'name' || $attribute == 'value')
		{
			return isset($this->pop_image[$attribute]);
		}

		return parent::offsetExists($attribute);
	}

	public function offsetSet($attribute, $value)
	{
		if ($attribute == 'name' || $attribute == 'value')
		{
			$this->pop_image[$attribute] = $value;

			return;
		}

		parent::offsetSet($attribute, $value);
	}

	public function offsetGet($attribute)
	{
		if ($attribute == 'name' || $attribute == 'value')
		{
			return $this->pop_image[$attribute];
		}

		return parent::offsetGet($attribute);
	}

	public function offsetUnset($attribute)
	{
		if ($attribute == 'name' || $attribute == 'value')
		{
			unset($this->pop_image[$attribute]);

			return;
		}

		parent::offsetUnset($attribute);
	}
}

class UploadImage extends \Brickrouge\File
{
	const UPLOAD_MODE = '#uploadimage-upload-mode';
	const UPLOAD_MODE_CREATE = 'create';
	const UPLOAD_MODE_UPDATE = 'update';

	protected function alter_dataset(array $dataset)
	{
		return parent::alter_dataset($dataset) + [

			'upload-mode' => $this[self::UPLOAD_MODE] ?: self::UPLOAD_MODE_CREATE

		];
	}
}
