<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Prototype;

require __DIR__ . '/../vendor/autoload.php';

#
# Set the `thumbnail` prototype method, which is usually set by the Thumbnailer module.
#

$prototype = Prototype::get('Icybee\Modules\Images\Image');
$prototype['thumbnail'] = 'ICanBoogie\Modules\Thumbnailer\Hooks::method_thumbnail';