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

use Brickrouge\Document;

use Icybee\Modules\Images as Root;
use Icybee\Modules\Images\Module;

class ManageBlock extends \Icybee\Modules\Files\Block\ManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(Root\DIR . 'public/slimbox.js');
		$document->css->add(Root\DIR . 'public/slimbox.css');
		$document->js->add(__DIR__ . '/ManageBlock.js');
	}

	public function __construct(Module $module, array $attributes)
	{
		parent::__construct($module, $attributes + [

			self::T_COLUMNS_ORDER => [

				'title', 'size', 'download', 'is_online', 'uid', 'surface', 'updated_at'

			]
		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `surface`: An instance of {@link ManageBlock\SurfaceColumn}.
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			'title' => ManageBlock\TitleColumn::class,
			'surface' => ManageBlock\SurfaceColumn::class

		]);
	}
}
