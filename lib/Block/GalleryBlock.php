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
use Brickrouge\Element;

use Icybee\Modules\Images\Image;

/**
 * @property Image[] $records
 */
class GalleryBlock extends ManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add('GalleryBlock.css');
	}

	public function __construct($module, array $tags = [])
	{
		parent::__construct($module, $tags + [

			self::T_ORDER_BY => 'title',
			self::T_BLOCK => 'gallery'

		]);
	}

	protected function resolve_options($name, array $modifiers)
	{
		return parent::resolve_options($name . '/gallery', $modifiers);
	}

	protected function render_body()
	{
		$rendered_columns_cells = $this->render_columns_cells($this->columns);
		$rows = $this->columns_to_rows($rendered_columns_cells);

		$html = '';

		foreach ($rows as $i => $row)
		{
			$record = $this->records[$i];
			$title =  $record->title;

			$label = new Element('a', [

				Element::INNER_HTML => \ICanBoogie\escape($title),

				'class' => 'goto-edit',
				'title' => $this->t('Edit this item'),
				'href' => $this->app->url_for("admin:{$record->constructor}:edit", $record)

			]);

			$img = $record->thumbnail('$gallery')->to_element([

				'title' => $title,
				'alt' => $title

			]);

			$html .= <<<EOT
<div class="thumbnailer-wrapper" data-key="{$record->nid}" style="width: 128px;">
	<a href="{$record->pathname->relative}" rel="lightbox[]">$img</a>
	$label
</div>
EOT;
		}

		$colspan = count($this->columns) + 1;

		return <<<EOT
<tr id="gallery">
	<td colspan="{$colspan}" class="gallery-inner">
		<div class="gallery-contents">$html</div>
	</td>
</tr>
EOT;
	}
}