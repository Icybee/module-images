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

use ICanBoogie\I18n;
use ICanBoogie\Modules\Thumbnailer\Thumbnail;
use ICanBoogie\Operation;

use Brickrouge\Element;

class ImageUpload extends \Icybee\Modules\Files\FileUpload
{
	const THUMBNAIL_WIDTH = 64;
	const THUMBNAIL_HEIGHT = 64;

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/slimbox.js');
		$document->css->add(DIR . 'public/slimbox.css');
		$document->js->add(DIR . 'public/module.js');
		$document->css->add(DIR . 'public/module.css');
	}

	protected function preview($path)
	{
		$w = $this->w;
		$h = $this->h;

		$thumbnail = new Thumbnail($path, [

			'w' => $w,
			'h' => $h,
			'format' => 'jpeg',
			'background' => 'silver,white,medium'

		]);

		$img = new Element('img', [

			'src' => $thumbnail->url . '&uniqid=' . uniqid(),
			'width' => $w,
			'height' => $h,
			'alt' => ''

		]);

		$repository = \ICanBoogie\app()->config['repository.temp'];

		if (strpos($path, $repository) === 0)
		{
			return $img;
		}

		return '<a href="' . $path . '?uniqid=' . uniqid() . '" rel="lightbox">' . $img . '</a>';
	}

	protected function details($path)
	{
		$path = $this['value'];

		list($entry_width, $entry_height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $path);

		$w = $entry_width;
		$h = $entry_height;

		#
		# if the image is larger then the thumbnail dimensions, we resize the image using
		# the "surface" mode.
		#

		$resized = false;

		if (($w * $h) > (self::THUMBNAIL_WIDTH * self::THUMBNAIL_HEIGHT))
		{
			$resized = true;

			$ratio = sqrt($w * $h);

			$w = round($w / $ratio * self::THUMBNAIL_WIDTH);
			$h = round($h / $ratio * self::THUMBNAIL_HEIGHT);
		}

		$this->w = $w;
		$this->h = $h;

		#
		# infos
		#

		$details = [

			$this->t('Image size: {0}×{1}px', [ $entry_width, $entry_height ])

		];

		if (($entry_width != $w) || ($entry_height != $h))
		{
			$details[] = $this->t('Display ratio: :ratio%', [ ':ratio' => round(($w * $h) / ($entry_width * $entry_height) * 100) ]);
		}
		else
		{
			$details[] = $this->t('Displayed as is');
		}

		$details[] = I18n\format_size(filesize($_SERVER['DOCUMENT_ROOT'] . $path));

		return $details;
	}
}
