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

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

global $core;

$core = new \ICanBoogie\Core(\ICanBoogie\array_merge_recursive(\ICanBoogie\get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config'

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$core();

#
# Install modules
#

$errors = new \ICanBoogie\Errors();

foreach (array_keys($core->modules->enabled_modules_descriptors) as $module_id)
{
	#
	# The index on the `constructor` column of the `nodes` module clashes with SQLite, we don't
	# care right now, so the exception is discarted.
	#

	try
	{
		$core->modules[$module_id]->install($errors);
	}
	catch (\Exception $e)
	{
		$errors[$module_id] = "Unable to install module: " . $e->getMessage();
	}
}

if ($errors->count())
{
	foreach ($errors as $error)
	{
		echo "$module_id: $error\n";
	}

	exit(1);
}

#
#
#

$core->site_id = 0;

use Icybee\Modules\Users\User;

$user = User::from([

	'username' => 'admin',
	'email' => 'admin@example.com'

]);

$user->save();

#
#
#

$thumbnailer_version = $core->thumbnailer_versions;
$thumbnailer_version['articles-list'] = 'w:120;h:100';
$thumbnailer_version['articles-view'] = 'w:420;h:340';


/*
use ICanBoogie\Modules\Thumbnailer\Versions;

$thumbnailer_versions = new Versions
(
	array
	(
		'articles-list' => 'w:120;h:100',
		'articles-view' => 'w:420;h:340'
	)
);

$thumbnailer_versions->save();
*/

/*
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
*/

namespace Tests\Icybee\Modules\Images;

class FakeSaveOperation extends \Icybee\Modules\Images\SaveOperation
{
	protected function get_controls()
	{
		return [

			self::CONTROL_AUTHENTICATION => false,
			self::CONTROL_FORM => false

		] + parent::get_controls();
	}
}