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

use Brickrouge\Element;

class PopOrUploadImage extends Element
{
	private $pop_image;

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add('widget-pop-or-upload-image.css');
		$document->js->add('widget-pop-or-upload-image.js');
	}

	public function __construct(array $attributes=array())
	{
		parent::__construct
		(
			'div', $attributes + array
			(
				Element::CHILDREN => array
				(
					$this->pop_image = new PopImage,
					$this->upload_image = new UploadImage
					(
						array
						(
							UploadImage::FILE_WITH_LIMIT => true,

							'data-name' => Image::PATH,
							'data-upload-url' => Operation::encode('images/save')
						)
					)
				),

				Element::WIDGET_CONSTRUCTOR => 'PopOrUploadImage'
			)
		);
	}

	protected function alter_class_names(array $class_names)
	{
		return parent::alter_class_names($class_names) + array
		(
			'widget-class' => 'widget-pop-or-upload-image'
		);
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

}