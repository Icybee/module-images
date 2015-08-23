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

use Icybee\Modules\Nodes\Node;

class NodeRelationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Image
	 */
	static private $record;

	static public function setupBeforeClass()
	{
		copy(__DIR__ . '/resources/claire.png', \ICanBoogie\REPOSITORY . 'tmp/claire.png');

		$record = Image::from([

			Image::HTTP_FILE => \ICanBoogie\HTTP\File::from([ 'pathname' => \ICanBoogie\REPOSITORY . 'tmp/claire.png' ])

		]);

		$record->save();

		self::$record = $record;
	}

	static public function  tearDownAfterClass()
	{
		if (self::$record)
		{
			self::$record->delete();
		}
	}

	public function testCanGetNodeAndImage()
	{
		$node = new Node;
		$image = new Image;
		$relation = new NodeRelation($node, $image);

		$this->assertSame($node, $relation->node);
		$this->assertSame($image, $relation->image);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function testCannotSetNode()
	{
		$relation = new NodeRelation(new Node, new Image);
		$relation->node = null;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function testCannotSetImage()
	{
		$relation = new NodeRelation(new Node, new Image);
		$relation->image = null;
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function testCannotSetAnything()
	{
		$relation = new NodeRelation(new Node, new Image);
		$relation->anything = null;
	}

	public function testShortVersion()
	{
		$node = Node::from(array('constructor' => 'articles'));
		$image = Image::from(array('nid' => 1));

		$relation = new NodeRelation($node, $image);
		$this->assertEquals('/api/images/1/thumbnails/articles-list', $relation->thumbnail(':list')->url);
		$this->assertEquals('/api/images/1/thumbnails/articles-view', $relation->thumbnail->url);
	}

	public function testPrototypeGetter()
	{
		$node = Node::from(array('constructor' => 'articles'));
		$node->metas = array('image_id' => 1);

		$this->assertInstanceOf(__NAMESPACE__ . '\NodeRelation', $node->image);
	}
}
