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

use ICanBoogie\HTTP\File as HTTPFile;

class ThumbnailTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Image
	 */
	static private $image;

	static public function setupBeforeClass()
	{
		$pathname = \ICanBoogie\REPOSITORY . 'tmp/claire.png';

		copy(\ICanBoogie\DOCUMENT_ROOT . 'resources/claire.png', $pathname);

		self::$image = Image::from([

			Image::HTTP_FILE => HTTPFile::from([ 'pathname' => $pathname ]),

		]);

		self::$image->save();
	}

	static public function tearDownAfterClass()
	{
		self::$image->delete();
	}

	/**
	 * @dataProvider provide_test_url
	 */
	public function test_url($expected, $options)
	{
		$image = self::$image;
		$thumbnail = new Thumbnail($image, $options);
		$separator = strpos($expected, '?') === false ? '?' : '&';

		$this->assertEquals("/images/{$image->uuid}/$expected{$separator}{$image->short_hash}", $thumbnail->url);
	}

	public function provide_test_url()
	{
		return [

			[ "64x64", [

				'w' => 64,
				'h' => 64,
				'method' => 'fill'

			] ],

			[ "64x64/fit", [

				'w' => 64,
				'h' => 64,
				'method' => 'fit'

			] ],

			[ "64x64.gif", [

				'w' => 64,
				'h' => 64,
				'method' => 'fill',
				'format' => 'gif'

			] ],

			[ "64x64/fit.gif", [

				'w' => 64,
				'h' => 64,
				'method' => 'fit',
				'format' => 'gif'

			] ],

			[ "64x64/fit.gif?b=red%2Cblue", [

				'w' => 64,
				'h' => 64,
				'method' => 'fit',
				'format' => 'gif',
				'background' => 'red,blue'

			] ]

		];
	}
}
