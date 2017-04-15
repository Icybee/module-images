<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use Icybee\Modules\Users\User;

chdir(__DIR__);

require __DIR__ . '/../vendor/autoload.php';

$app = boot();
$app->modules->install();

#
#
#

$thumbnailer_version = $app->thumbnailer_versions;
$thumbnailer_version['articles-list'] = 'w:120;h:100';
$thumbnailer_version['articles-view'] = 'w:420;h:340';

User::from([

	User::USERNAME => 'admin',
	User::EMAIL => 'admin@example.com',

])->save();
