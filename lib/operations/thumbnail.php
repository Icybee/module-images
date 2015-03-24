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

use ICanBoogie\HTTP\Request;
use ICanBoogie\Modules\Thumbnailer\Version;

/**
 * Creates a thumbnail of an image managed by the "images" module.
 */
class ThumbnailOperation extends \ICanBoogie\Modules\Thumbnailer\GetOperation
{
	protected function resolve_version(Request $request)
	{
		if (isset($request->path_params['size']))
		{
			$version = Version::from_uri($request->uri);
		}
		else
		{
			$version = parent::resolve_version($request);
		}

		$version->src = $this->resolve_version_src($request);

		return $version;
	}

	protected function resolve_version_src(Request $request)
	{
		$nid = $request['nid'];
		$root = \ICanBoogie\DOCUMENT_ROOT;
		$files = glob(\ICanBoogie\REPOSITORY . "files/*/{$nid}-*");

		if (!$files)
		{
			return null;
		}

		return substr(array_shift($files), strlen($root));
	}
}
