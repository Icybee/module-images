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
		$this->assertEquals("/repository/files/image/1-claire.png", $record->path);
		$this->assertEquals("image/png", $record->mime);
		$this->assertEquals(filesize($source), $record->size);
		$this->assertEquals('claire', $record->title);
		$this->assertFileExists(dirname(\ICanBoogie\REPOSITORY) . $record->path);
		$this->assertObjectNotHasAttribute(Image::HTTP_FILE, $record);

		$record->title = "Madonna";
		$record->save();

		$this->assertEquals("/repository/files/image/1-madonna.png", $record->path);
		$this->assertFileExists(dirname(\ICanBoogie\REPOSITORY) . $record->path);

		$this->assertInstanceOf('Icybee\Modules\Images\Thumbnail', $record->thumbnail('w:64;h:64'));

		$record->delete();
		$this->assertFileNotExists(dirname(\ICanBoogie\REPOSITORY) . $record->path);
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
		$image = Image::from(array(

			'nid' => 16,
			'width' => 320,
			'height' => 240,
			'path' => '/repository/files/madonna.jpeg'

		));

		$this->assertEquals('<img src="/repository/files/madonna.jpeg" alt="" width="320" height="240" data-nid="16" />', (string) $image);

		$image = Image::from(array(

			'nid' => 16,
			'width' => 320,
			'height' => 240,
			'path' => '/repository/files/madonna.jpeg',
			'alt' => 'Madonna'

		));

		$this->assertEquals('<img src="/repository/files/madonna.jpeg" alt="Madonna" width="320" height="240" data-nid="16" />', (string) $image);
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
	 * @expectedException ICanBoogie\PropertyNotWritable
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