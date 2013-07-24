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

use Brickrouge\A;

/**
 * Decorates the provided component with a thumbnail.
 */
class ThumbnailDecorator
{
	protected $component;
	protected $record;
	protected $options = array
	(
		'version' => '$icon'
	);

	public function __construct($component, Image $record, array $options=array())
	{
		$this->component = $component;
		$this->record = $record;
		$this->options = $options + $this->options;
	}

	public function render()
	{
		$record = $this->record;
		$icon = $record->thumbnail($this->options['version'])->to_element
		(
			array
			(
				'data-popover-image' => $record->thumbnail('$popover')->url
			)
		);

		return new A($icon, $record->path, array('rel' => "lightbox[thumbnail-decorator]")) . $this->component;
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{
			return \Brickrouge\render_exception($e);
		}
	}
}