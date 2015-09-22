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

use ICanBoogie\HTTP\File as HTTPFile;
use ICanBoogie\Modules\Thumbnailer\Thumbnail;

use Icybee\Modules\Files\File;

/**
 * Representation of a managed image.
 *
 * @property-read int $surface The surface of the image, computed from the {@link $width} and
 * {@link $height} properties.
 *
 * @property-read \Icybee\Modules\Nodes\Node $consumer The node to which the image is associated.
 *
 * @method Thumbnail thumbnail($version, $additional_options = null)
 */
class Image extends File
{
	const MODEL_ID = 'images';

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
	 * Returns the surface of the image, computed from the {@link $width} and {@link $height}
	 * properties.
	 *
	 * @return int
	 */
	protected function get_surface()
	{
		return $this->width * $this->height;
	}

	/**
	 * Returns an `IMG` element.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (\Exception $e)
		{
			trigger_error($e->getMessage() . "\nin: " . $e->getFile() . ':' . $e->getLine(), E_USER_ERROR);

			return '';
		}
	}

	protected function save_file_before(HTTPFile $file)
	{
		parent::save_file_before($file);

		list($w, $h) = getimagesize($file->pathname);

		$this->width = $w;
		$this->height = $h;
	}

	public function render()
	{
		$alt = \ICanBoogie\escape($this->alt);

		return <<<EOT
<img src="/images/{$this->uuid}{$this->extension}" alt="$alt" width="{$this->width}" height="{$this->height}" data-nid="{$this->nid}" />
EOT;
	}
}
