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

class GalleryController extends \Icybee\Controller\BlockController
{
	protected function decorate_with_block($component)
	{
		$element = parent::decorate_with_block($component)->render();

		$element->add_class('block--manage');

		return $element;
	}
}
