<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images\Block;

use Brickrouge\Group;
use Brickrouge\Text;

use Icybee\Modules\Images\ImageUpload;

class EditBlock extends \Icybee\Modules\Files\Block\EditBlock
{
	protected $accept = [

		'image/gif', 'image/png', 'image/jpeg'

	];

	protected $uploader_class = ImageUpload::class;

	protected function lazy_get_children()
	{
		return array_merge(parent::lazy_get_children(), [

			'alt' => new Text([

				Group::LABEL => 'alt'

			])

		]);
	}
}
