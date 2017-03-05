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

use function ICanBoogie\app;
use ICanBoogie\Modules\Thumbnailer\Version;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
	static private $thumbnailer_versions;

	static public function setupBeforeClass()
	{
		self::$thumbnailer_versions = app()->thumbnailer_versions;
	}

	/**
	 * @dataProvider provide_test_thumbnail_version
	 *
	 * @param string $id
	 * @param string $expected
	 */
	public function test_thumbnail_version($id, $expected)
	{
		$version = self::$thumbnailer_versions[$id];

		$this->assertInstanceOf(Version::class, $version);
		$this->assertEquals($expected, (string) $version);
	}

	public function provide_test_thumbnail_version()
	{
		$versions = [];

		foreach (Module::THUMBNAIL_VERSIONS as $id => $definition)
		{
			$versions[] = [ $id, $definition ];
		}

		return $versions;
	}
}
