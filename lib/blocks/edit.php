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

use Brickrouge\Group;
use Brickrouge\Text;

class EditBlock extends \Icybee\Modules\Files\EditBlock
{
	protected $accept = array
	(
		'image/gif', 'image/png', 'image/jpeg'
	);

	protected $uploader_class = 'Icybee\Modules\Images\ImageUpload';

	protected function lazy_get_children()
	{
		return array_merge
		(
			parent::lazy_get_children(), array
			(
				'alt' => new Text
				(
					array
					(
						Group::LABEL => 'alt'
					)
				)
			)
		);
	}
}