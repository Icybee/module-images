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
	public function test_get_node()
	{
		$node = Node::from();
		$image = Image::from();
		$relation = new NodeRelation($node, $image);

		$this->assertSame($node, $relation->node);
	}

	public function test_get_image()
	{
		$node = Node::from();
		$image = Image::from();
		$relation = new NodeRelation($node, $image);

		$this->assertSame($image, $relation->image);
	}

	public function test_get_thumbnail()
	{
		$thumbnail = $this
			->getMockBuilder(Thumbnail::class)
			->disableOriginalConstructor()
			->getMock();

		$relation = $this
			->getMockBuilder(NodeRelation::class)
			->disableOriginalConstructor()
			->setMethods([ 'thumbnail' ])
			->getMock();
		$relation
			->expects($this->once())
			->method('thumbnail')
			->with(':view')
			->willReturn($thumbnail);

		/* @var $relation NodeRelation */

		$this->assertSame($thumbnail, $relation->thumbnail);
	}

	public function test_should_forward_undefined_properties()
	{
		$uuid = \ICanBoogie\generate_v4_uuid();
		$node = Node::from();
		$image = Image::from([ 'uuid' => $uuid ]);
		$relation = new NodeRelation($node, $image);

		$this->assertSame($uuid, $relation->uuid);
	}

	/**
	 * @expectedException \ICanBoogie\PropertyNotWritable
	 */
	public function test_immutable()
	{
		$node = Node::from();
		$image = Image::from();
		$relation = new NodeRelation($node, $image);
		$relation->{ uniqid() } = uniqid();
	}

	public function test_should_forward_undefined_methods()
	{
		$node = Node::from();

		$method = 'method' . uniqid();
		$arg1 = uniqid();
		$arg2 = uniqid();
		$rc = uniqid();

		$image = $this
			->getMockBuilder(Image::class)
			->disableOriginalConstructor()
			->setMethods([ $method ])
			->getMockForAbstractClass();
		$image
			->expects($this->once())
			->method($method)
			->with($arg1, $arg2)
			->willReturn($rc);

		/* @var $image Image */

		$relation = new NodeRelation($node, $image);
		$this->assertSame($rc, $relation->$method($arg1, $arg2));
	}

	public function test_to_string()
	{
		$node = Node::from();

		$expected = 'string' . uniqid();

		$image = $this
			->getMockBuilder(Image::class)
			->disableOriginalConstructor()
			->setMethods([ '__toString' ])
			->getMockForAbstractClass();
		$image
			->expects($this->once())
			->method('__toString')
			->willReturn($expected);

		/* @var $image Image */

		$relation = new NodeRelation($node, $image);
		$this->assertSame($expected, (string) $relation);
	}

	public function test_thumbnail()
	{
		$constructor = 'c' . uniqid();
		$node = Node::from([ Node::CONSTRUCTOR => $constructor ]);
		$image = Image::from();
		$relation = new NodeRelation($node, $image);
		$version = 'v' . uniqid();
		$thumbnail = $relation->thumbnail(':' . $version);

		$reflection = new \ReflectionObject($thumbnail);
		$reflection_property = $reflection->getProperty('version_name');
		$reflection_property->setAccessible(true);

		$this->assertSame($constructor . '-' . $version, $reflection_property->getValue($thumbnail));
	}
}
