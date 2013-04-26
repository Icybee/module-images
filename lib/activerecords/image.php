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
	public $alt;

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

	/**
	 * Returns the surface of the image, computed from the {@link $width} and {@link $height}
	 * properties.
	 *
	 * @return int
	 */
	protected function volatile_get_surface()
	{
		return $this->width * $this->height;
	}
}