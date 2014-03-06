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

use ICanBoogie\Modules\Thumbnailer\Version;
use ICanBoogie\Modules\Thumbnailer\Versions;
use ICanBoogie\Prototype;

require __DIR__ . '/../vendor/autoload.php';

#
# Thumbnailer setup
#
$prototype = Prototype::from(__NAMESPACE__ . '\Image');
$prototype['thumbnail'] = 'ICanBoogie\Modules\Thumbnailer\Hooks::method_thumbnail';

#
# Image setup
#

$prototype = Prototype::from('Icybee\Modules\Nodes\Node');
$prototype['lazy_get_image'] = __NAMESPACE__ . '\Hooks::prototype_get_image';

#
# Mocking core
#

$core = (object) array
(
	'models' => array
	(
		'images' => array
		(
			1 => Image::from(array('nid' => 1)),
			2 => Image::from(array('nid' => 2)),
			3 => Image::from(array('nid' => 3))
		)
	),

	'thumbnailer_versions' => new Versions
	(
		array
		(
			'articles-list' => new Version('w:120;h:100'),
			'articles-view' => new Version('w:420;h:340')
		)
	)
);