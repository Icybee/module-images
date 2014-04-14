<?php

namespace Icybee\Modules\Images;

class GalleryController extends \Icybee\BlockController
{
	protected function decorate_with_block($component)
	{
		$element = parent::decorate_with_block($component)->render();

		$element->add_class('block--manage');

		return $element;
	}
}