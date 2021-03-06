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

use ICanBoogie\ErrorCollection;

use Icybee\Binding\Core\PrototypedBindings;

class Module extends \Icybee\Modules\Files\Module
{
	use PrototypedBindings;

	const ICON_WIDTH = 24;
	const ICON_HEIGHT = 24;
	const THUMBNAIL_WIDTH = 200;
	const THUMBNAIL_HEIGHT = 200;

	static private $thumbnail_versions = [

		'$icon' => "24x24.jpg",
		'$icon-m' => "128x128.jpg",
		'$popimage' => "96x96/surface.jpg",
		'$popover' => "200x200/surface.jpg?nu=1",
		'$gallery' => "128x128/constrained.jpg"

	];

	/**
	 * Checks that the thumbnail versions are defined.
	 *
	 * @inheritdoc
	 */
	public function is_installed(ErrorCollection $errors)
	{
		$versions = $this->app->thumbnailer_versions;

		foreach (self::$thumbnail_versions as $version => $options)
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

		foreach (self::$thumbnail_versions as $version => $options)
		{
			$versions[$version] = $options;
		}

		$versions->save();

		return parent::install($errors);
	}
}
