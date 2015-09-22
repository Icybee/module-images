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

class ImageTest extends \PHPUnit_Framework_TestCase
{
	public function test_save()
	{
		$source = __DIR__ . '/resources/claire.png';

		copy($source, \ICanBoogie\REPOSITORY . 'tmp/claire.png');

		$record = Image::from([

			Image::HTTP_FILE => \ICanBoogie\HTTP\File::from([ 'pathname' => \ICanBoogie\REPOSITORY . 'tmp/claire.png' ])

		]);

		$nid = $record->save();

		$this->assertEquals($nid, $record->nid);
		$this->assertEquals(200, $record->width);
		$this->assertEquals(200, $record->height);
		$this->assertEquals("image/png", $record->mime);
		$this->assertEquals(filesize($source), $record->size);
		$this->assertEquals('claire', $record->title);
		$this->assertFileExists((string) $record->pathname);
		$this->assertObjectNotHasAttribute(Image::HTTP_FILE, $record);

		$record->title = "Madonna";
		$record->save();

		$this->assertFileExists((string) $record->pathname);

		$this->assertInstanceOf('Icybee\Modules\Images\Thumbnail', $record->thumbnail('w:64;h:64'));

		$record->delete();
		$this->assertFileNotExists((string) $record->pathname);
	}

	public function test_get_model_id()
	{
		$image = new Image;
		$this->assertEquals('images', $image->model_id);
	}

	public function testThumbnail()
	{
		$image = new Image;
		$image->path = '/path/to/image.png';

		$thumbnail = $image->thumbnail('w:128;h:128');

		$this->assertInstanceOf('ICanBoogie\Modules\Thumbnailer\Thumbnail', $thumbnail);
	}

	public function testToString()
	{
		$uuid = \ICanBoogie\generate_v4_uuid();
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
		$image = Image::from(array(

			'width' => 320,
			'height' => 240,

		));

		$this->assertEquals(320 * 240, $image->surface);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_set_surface()
	{
		$image = Image::from(array(

			'width' => 320,
			'height' => 240,

		));

		$image->surface = 12;
	}
}
