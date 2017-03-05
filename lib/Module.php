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

use ICanBoogie\Binding\PrototypedBindings;
use ICanBoogie\ErrorCollection;

class Module extends \Icybee\Modules\Files\Module
{
	use PrototypedBindings;

	const ICON_WIDTH = 24;
	const ICON_HEIGHT = 24;
	const THUMBNAIL_WIDTH = 200;
	const THUMBNAIL_HEIGHT = 200;

	const THUMBNAIL_VERSION_ICON = "24x24.jpeg";
	const THUMBNAIL_VERSION_ICON_M = "128x128.jpeg";
	const THUMBNAIL_VERSION_POP_IMAGE = "96x96/surface.jpeg";
	const THUMBNAIL_VERSION_POPOVER = "200x200/surface.jpeg?nu=1";
	const THUMBNAIL_VERSION_GALLERY = "128x128/constrained.jpeg";

	const THUMBNAIL_VERSIONS = [

		'$icon'     => self::THUMBNAIL_VERSION_ICON,
		'$icon-m'   => self::THUMBNAIL_VERSION_ICON_M,
		'$popimage' => self::THUMBNAIL_VERSION_POP_IMAGE,
		'$popover'  => self::THUMBNAIL_VERSION_POPOVER,
		'$gallery'  => self::THUMBNAIL_VERSION_GALLERY

	];

	/**
	 * Checks that the thumbnail versions are defined.
	 *
	 * @inheritdoc
	 */
	public function is_installed(ErrorCollection $errors)
	{
		$versions = $this->app->thumbnailer_versions;

		foreach (self::THUMBNAIL_VERSIONS as $version => $options)
		{
			if (isset($versions[$version]))
			{
				continue;
			}

			$errors->add($this->id, "Thumbnail version %version is not defined.", [ 'version' => $version ]);
		}

		return parent::is_installed($errors);
	}

	/**
	 * Define thumbnail versions.
	 *
	 * @inheritdoc
	 */
	public function install(ErrorCollection $errors)
	{
		$versions = $this->app->thumbnailer_versions;

		foreach (self::THUMBNAIL_VERSIONS as $version => $options)
		{
			$versions[$version] = $options;
		}

		$versions->save();

		return parent::install($errors);
	}
}
