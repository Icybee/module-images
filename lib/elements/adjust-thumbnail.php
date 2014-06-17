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

use ICanBoogie\Modules\Thumbnailer\AdjustThumbnailOptions;

use Brickrouge\Element;

class AdjustThumbnail extends \Brickrouge\Widget
{
	protected $adjust_image;
	protected $adjust_thumbnail_options;

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
			'div', $attributes + array
			(
				Element::CHILDREN => array
				(
					$this->adjust_image = new AdjustImage,
					$this->adjust_thumbnail_options = new AdjustThumbnailOptions
				),

				Element::WIDGET_CONSTRUCTOR => 'AdjustThumbnail'
			)
		);
	}

	public function render_inner_html()
	{
		return '<input type="hidden" value="' . \Brickrouge\escape($this['value']) . '" />'
		. parent::render_inner_html()
		. '<div class="more"><i class="icon-cog"></i></div>';
	}

	public function offsetSet($attribute, $value)
	{
		if ($attribute == 'value')
		{
			if (preg_match('/\/api\/images\/(\d+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/', $value, $matches))
			{
				list($path, $nid, , $width, $height, , $method) = $matches + array(3 => null, 4 => null, 6 => null);

				$options = array();

				$qs = strpos($value, '?');

				if ($qs)
				{
					parse_str(substr($value, $qs + 1), $options);
				}

				$options['width'] = $width;
				$options['height'] = $height;

				if ($method)
				{
					$options['method'] = $method;
				}

				$this->adjust_image['value'] = $nid;
				$this->adjust_thumbnail_options['value'] = $options;
			}
		}

		parent::offsetSet($attribute, $value);
	}

	// TODO-20130605: offsetGet >> value
}

namespace Brickrouge\Widget;

class AdjustThumbnail extends \Icybee\Modules\Images\AdjustThumbnail
{

}