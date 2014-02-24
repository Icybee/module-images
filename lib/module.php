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

use ICanBoogie\Errors;
use ICanBoogie\I18n\FormattedString;

use ICanBoogie\Modules\Thumbnailer\Versions;

class Module extends \Icybee\Modules\Files\Module
{
	const ICON_WIDTH = 24;
	const ICON_HEIGHT = 24;
	const THUMBNAIL_WIDTH = 200;
	const THUMBNAIL_HEIGHT = 200;

	static private $thumbnail_versions = array
	(
		'$icon' => array
		(
			'w' => self::ICON_WIDTH,
			'h' => self::ICON_HEIGHT,
			'format' => 'png'
		),

		'$icon-m' => array
		(
			'w' => 64,
			'h' => 64
		),

		'$popimage' => array
		(
			'w' => 96,
			'h' => 96,
			'method' => 'surface'
		),

		'$popover' => array
		(
			'w' => self::THUMBNAIL_WIDTH,
			'h' => self::THUMBNAIL_HEIGHT,
			'method' => 'surface',
			'no-upscale' => true,
			'quality' => 90
		),

		'$gallery' => array
		(
			'w' => 128,
			'h' => 128,
			'method' => 'constrained',
			'quality' => 90
		)
	);

	/**
	 * Checks that the thumbnail versions are defined.
	 */
	public function is_installed(Errors $errors)
	{
		global $core;

		$versions = $core->thumbnailer_versions;

		foreach (self::$thumbnail_versions as $version => $options)
		{
			if (isset($versions[$version]))
			{
				continue;
			}

			$errors[$this->id] = new FormattedString("Thumbnail version %version is not defined.", array('version' => $version));
		}

		return parent::is_installed($errors);
	}

	/**
	 * Define thumbnail versions.
	 */
	public function install(Errors $errors)
	{
		global $core;

		$versions = $core->thumbnailer_versions;

		foreach (self::$thumbnail_versions as $version => $options)
		{
			$versions[$version] = $options;
		}

		$versions->save();

		var_dump($versions);

		return parent::install($errors);
	}
}