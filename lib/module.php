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

use Icybee\Binding\Core\PrototypedBindings;

class Module extends \Icybee\Modules\Files\Module
{
	use PrototypedBindings;

	const ICON_WIDTH = 24;
	const ICON_HEIGHT = 24;
	const THUMBNAIL_WIDTH = 200;
	const THUMBNAIL_HEIGHT = 200;

	static private $thumbnail_versions = [

		'$icon' => "24x24.png",
		'$icon-m' => "64x64",
		'$popimage' => "96x96/surface",
		'$popover' => "200x200/surface?nu=1",
		'$gallery' => "128x128/constrained"

	];

	/**
	 * Checks that the thumbnail versions are defined.
	 *
	 * @inheritdoc
	 */
	public function is_installed(Errors $errors)
	{
		$versions = $this->app->thumbnailer_versions;

		foreach (self::$thumbnail_versions as $version => $options)
		{
			if (isset($versions[$version]))
			{
				continue;
			}

			$errors[$this->id] = $errors->format("Thumbnail version %version is not defined.", [ 'version' => $version ]);
		}

		return parent::is_installed($errors);
	}

	/**
	 * Define thumbnail versions.
	 *
	 * @inheritdoc
	 */
	public function install(Errors $errors)
	{
		$versions = $this->app->thumbnailer_versions;

		foreach (self::$thumbnail_versions as $version => $options)
		{
			$versions[$version] = $options;
		}

		$versions->save();

		return parent::install($errors);
	}
}
