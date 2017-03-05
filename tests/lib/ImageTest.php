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
use function ICanBoogie\generate_v4_uuid;

use ICanBoogie\AppConfig;
use ICanBoogie\HTTP\File as RequestFile;
use ICanBoogie\Modules\Thumbnailer\Thumbnail as ThumbnailerThumbnail;
use Icybee\Modules\Images\Thumbnail as ImageThumbnail;

class ImageTest extends \PHPUnit_Framework_TestCase
{
	private $repository_tmp;

	public function setUp()
	{
		$this->repository_tmp = app()->config[AppConfig::REPOSITORY_TMP];
	}

	public function test_save()
	{
		$filename = "claire.png";
		$source = __DIR__ . "/resources/$filename";
		$destination = "$this->repository_tmp/$filename";

		copy($source, $destination);

		$image = Image::from([

			Image::HTTP_FILE => RequestFile::from([ 'pathname' => $destination ])

		]);

		$nid = $image->save();

		$this->assertEquals($nid, $image->nid);
		$this->assertEquals(200, $image->width);
		$this->assertEquals(200, $image->height);
		$this->assertEquals("image/png", $image->mime);
		$this->assertEquals(filesize($source), $image->size);
		$this->assertEquals('claire', $image->title);
		$this->assertFileExists((string) $image->pathname);
		$this->assertObjectNotHasAttribute(Image::HTTP_FILE, $image);

		$image->title = "Madonna";
		$image->save();

		$this->assertFileExists((string) $image->pathname);
		$this->assertInstanceOf(ImageThumbnail::class, $image->thumbnail('w:64;h:64'));

		$pathname = (string) $image->pathname;
		$image->delete();
		$this->assertFileNotExists($pathname);
	}

	public function test_get_model_id()
	{
		$image = new Image;
		$this->assertEquals('images', $image->model_id);
	}

	public function testThumbnail()
	{
		$image = new Image;
		$thumbnail = $image->thumbnail('w:128;h:128');

		$this->assertInstanceOf(ThumbnailerThumbnail::class, $thumbnail);
	}

	public function testToString()
	{
		$uuid = generate_v4_uuid();
		$extension = '.jpeg';

		$image = Image::from([

			'nid' => 16,
			'uuid' => $uuid,
			'width' => 320,
			'height' => 240,
			'extension' => $extension

		]);

		$this->assertEquals('<img src="/images/' . $uuid . $extension . '" alt="" width="320" height="240" data-nid="16" />', (string) $image);

		$image = Image::from([

			'nid' => 16,
			'uuid' => $uuid,
			'width' => 320,
			'height' => 240,
			'extension' => $extension,
			'alt' => "Madonna"

		]);

		$this->assertEquals('<img src="/images/' . $uuid . $extension . '" alt="Madonna" width="320" height="240" data-nid="16" />', (string) $image);
	}

	public function test_get_surface()
	{
		$image = Image::from([

			'width' => 320,
			'height' => 240,

		]);

		$this->assertEquals(320 * 240, $image->surface);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_surface()
	{
		Image::from([ 'surface' => 12 ]);
	}
}
