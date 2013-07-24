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
use Brickrouge\Decorator;

/**
 * Decorates a component with a thumbnail.
 */
class ThumbnailDecorator extends Decorator
{
	protected $record;
	protected $options = array
	(
		'version' => '$icon'
	);

	/**
	 * Initializes the {@link $record} and {@link $options} properties.
	 *
	 * @param mixed $component The component to decorate.
	 * @param Image $record The source of the thumbnail.
	 * @param array $options Options.
	 */
	public function __construct($component, Image $record, array $options=array())
	{
		$this->record = $record;
		$this->options = $options + $this->options;

		parent::__construct($component);
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

		return new A($icon, $record->path, array('rel' => "lightbox[thumbnail-decorator]")) . parent::render();
	}
}