<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images\Routing;

use ICanBoogie\HTTP\ClientError;
use ICanBoogie\HTTP\NotFound;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Modules\Thumbnailer\Version;

use Icybee\Modules\Files\Binding\CoreBindings;

class ThumbnailController extends \ICanBoogie\Modules\Thumbnailer\Routing\ThumbnailController
{
	use CoreBindings;

	/**
	 * @inheritdoc
	 */
	protected function resolve_version(Request $request)
	{
		if (isset($request->path_params['size']))
		{
			return Version::from_uri($request->uri);
		}

		return parent::resolve_version($request);
	}

	/**
	 * @inheritdoc
	 */
	protected function resolve_source(Version $version)
	{
		$request = $this->request;
		$reference = $request['nid'] ?: $request['uuid'];

		if (!$reference)
		{
			throw new ClientError("Image identifier is empty.");
		}

		$pathname = $this->file_storage->find($reference);

		if (!$pathname)
		{
			throw new NotFound;
		}

		return $pathname;
	}
}
