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

class ModuleTest extends \PHPUnit_Framework_TestCase
{
	static private $app;
	static private $module;

	static public function setupBeforeClass()
	{
		self::$app = \ICanBoogie\app();
		self::$module = self::$app->modules['images'];
	}

	/**
	 * @dataProvider provide_test_thumbnail_version
	 */
	public function test_thumbnail_version($id, $expected)
	{
		$version = self::$app->thumbnailer_versions[$id];

		$this->assertInstanceOf('ICanBoogie\Modules\Thumbnailer\Version', $version);
		$this->assertEquals($expected, (string) $version);
	}

	public function provide_test_thumbnail_version()
	{
		return [

			[ '$icon',     '24x24.png' ],
			[ '$icon-m',   '64x64' ],
			[ '$popimage', '96x96/surface' ],
			[ '$popover',  '200x200/surface?nu=1' ],
			[ '$gallery',  '128x128/constrained' ]

		];
	}
}