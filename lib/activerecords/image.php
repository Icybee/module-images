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

/**
 * Representation of a managed image.
 *
 * @property-read int $surface The surface of the image, computed from the {@link $width} and
 * {@link $height} properties.
 *
 * @property-read \Icybee\Modules\Nodes\Node $consumer The node to which the image is associated.
 *
 * @method \ICanBoogie\Modules\Thumbnailer\Thumbnail thumbnail()
 */
class Image extends \Icybee\Modules\Files\File
{
	const WIDTH = 'width';
	const HEIGHT = 'height';
	const ALT = 'alt';

	/**
	 * Width of the image in pixels.
	 *
	 * @var int
	 */
	public $width;

	/**
	 * Height of the image in pixels.
	 *
	 * @var int
	 */
	public $height;

	/**
	 * Alternative text, used when the image cannot be displayed.
	 *
	 * @var string
	 */
	public $alt = '';

	/**
	 * Defaults the model to "images".
	 *
	 * @param string $model
	 */
	public function __construct($model='images')
	{
		parent::__construct($model);
	}

	/**
	 * Returns an `IMG` element.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$path = \ICanBoogie\escape($this->path);
		$alt = \ICanBoogie\escape($this->alt);

		return <<<EOT
<img src="$path" alt="$alt" width="{$this->width}" height="{$this->height}" data-nid="{$this->nid}" />
EOT;
	}

	public function save()
	{
		if (isset($this->{ self::HTTP_FILE }))
		{
			/* @var $file \ICanBoogie\HTTP\File */

			$file = $this->{ self::HTTP_FILE };

			list($w, $h) = getimagesize($file->pathname);

			$this->width = $w;
			$this->height = $h;
		}

		return parent::save();
	}

	/**
	 * Returns the surface of the image, computed from the {@link $width} and {@link $height}
	 * properties.
	 *
	 * @return int
	 */
	protected function get_surface()
	{
		return $this->width * $this->height;
	}
}
