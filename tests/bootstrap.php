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

use ICanBoogie\Core;
use ICanBoogie\Errors;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require __DIR__ . '/../vendor/autoload.php';

/* @var $app Core|\ICanBoogie\Module\CoreBindings|\Icybee\Modules\Sites\Binding\CoreBindings */

$app = new Core(\ICanBoogie\array_merge_recursive(\ICanBoogie\get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config' => 10

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$app->boot();

#
# Install modules
#

$errors = $app->modules->install(new Errors);

if ($errors->count())
{
	foreach ($errors as $module_id => $error)
	{
		if ($error instanceof \Exception)
		{
			$error = $error->getMessage();
		}

		echo "$module_id: $error\n";
	}

	exit(1);
}

#
#
#

$app->site_id = 0;

use Icybee\Modules\Users\User;

$user = User::from([

	'username' => 'admin',
	'email' => 'admin@example.com'

]);

$user->save();

#
#
#

$thumbnailer_version = $app->thumbnailer_versions;
$thumbnailer_version['articles-list'] = 'w:120;h:100';
$thumbnailer_version['articles-view'] = 'w:420;h:340';

namespace Tests\Icybee\Modules\Images;

class FakeSaveOperation extends \Icybee\Modules\Images\Operation\SaveOperation
{
	protected function get_controls()
	{
		return [

			self::CONTROL_AUTHENTICATION => false,
			self::CONTROL_FORM => false

		] + parent::get_controls();
	}
}
