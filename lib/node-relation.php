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

use ICanBoogie\PropertyNotWritable;

use Icybee\Modules\Nodes\Node;

/**
 * A representation of relation between an image and a node.
 *
 * @property-read Image $image The image associated with the node.
 * @property-read Node $node The node.
 * @property-read Thumbnail $thumbnail Thumbnail for the `view` version.
 */
class NodeRelation
{
	private $node;
	private $image;

	/**
	 * Initializes the {@link $node} and {@link $image} properties.
	 *
	 * @param Node $node
	 * @param Image $image
	 */
	public function __construct(Node $node, Image $image)
	{
		$this->node = $node;
		$this->image = $image;
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'node': return $this->node;
			case 'image': return $this->image;
			case 'thumbnail': return $this->thumbnail(':view');
			default: return $this->image->$property;
		}
	}

	/**
	 * @throws PropertyNotWritable in attempt to set a property.
	 *
	 * @inheritdoc
	 */
	public function __set($property, $value)
	{
		throw new PropertyNotWritable([ $property, $this ]);
	}

	public function __call($name, array $arguments)
	{
		return call_user_func_array([ $this->image, $name ], $arguments);
	}

	/**
	 * Returns an HTML representation of the image.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->image;
	}

	/**
	 * Returns a {@link Thumbnail} instance.
	 *
	 * @param string $version The version of the thumbnail. If the version starts with a column
	 * ":", it is removed and the node's constructor is prepended to the version. e.g. ":list"
	 * becomes "news-list" for a news node. This is referred to as "shorthand version".
	 *
	 * @param mixed $additional_options Additional options.
	 *
	 * @return Thumbnail
	 */
	public function thumbnail($version, $additional_options = null)
	{
		if ($version && $version{0} == ':')
		{
			$version = $this->node->constructor . '-' . substr($version, 1);
		}

		return new Thumbnail($this->image, $version, $additional_options);
	}
}
