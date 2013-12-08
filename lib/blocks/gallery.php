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

use ICanBoogie\I18n;
use ICanBoogie\Operation;

use Brickrouge\Element;

class GalleryBlock extends ManageBlock
{
	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add('gallery.css');
	}

	public function __construct($module, array $tags=array())
	{
		parent::__construct
		(
			$module, $tags + array
			(
				self::T_ORDER_BY => 'title',
				self::T_BLOCK => 'gallery'
			)
		);
	}

	protected function resolve_options($name, array $modifiers)
	{
		return parent::resolve_options($name . '/gallery', $modifiers);
	}

	protected function render_body()
	{
		global $core;

		$rendered_columns_cells = $this->render_columns_cells($this->columns);
		$rows = $this->columns_to_rows($rendered_columns_cells);

		$html = '';

		foreach ($rows as $i => $row)
		{
			$record = $this->records[$i];
			$title =  $record->title;

			$label = new Element
			(
				'a', array
				(
					Element::INNER_HTML => \ICanBoogie\escape($title),

					'class' => 'edit',
					'title' => I18n\t('Edit this item'),
					'href' => \ICanBoogie\Routing\contextualize("/admin/{$record->constructor}/{$record->nid}/edit")
				)
			);

			$label .= $row['surface'] . '<br />' . $row['updated_at'];

			$img = $record->thumbnail('$gallery')->to_element(array(

				'title' => $title,
				'alt' => $title

			));

			$key = '';

			$html .= <<<EOT
<div class="thumbnailer-wrapper" style="width: 128px;">
	<a href="{$record->path}" rel="lightbox[]">$img</a>
	<div class="key">$key</div>
	$label
</div>
EOT;
		}

// 		var_dump($rows);

		$colspan = count($this->columns) + 1;

		return <<<EOT
<tr id="gallery">
	<td colspan="{$colspan}">
	$html
	</td>
</tr>
EOT;









		$size = 128; // TODO-20110627: make this customizable
		$size = min($size, max($size, 16), 512);

		$module_id = $this->module->id;

		$order = $this->options->order_by;

		$rc = PHP_EOL . '<tr id="gallery"><td colspan="' . (count($this->columns) + 1) . '">';

		$user = $core->user;
		$context = $core->site->path;

		foreach ($this->records as $entry)
		{
			$title = $entry->title;
			$key = null;

			$label = new Element
			(
				'a', array
				(
					Element::INNER_HTML => \ICanBoogie\escape($title),

					'class' => 'edit',
					'title' => I18n\t('Edit this item'),
					'href' => $context . '/admin/' . $module_id . '/' . $entry->nid . '/edit'
				)
			);

			if ($size >= 64)
			{
				if ($user->has_ownership($module_id, $entry))
				{
					$key = new Element
					(
						Element::TYPE_CHECKBOX, array
						(
							'class' => 'key',
							'title' => I18n\t('Toggle selection for entry #\1', array($entry->nid)),
							'value' => $entry->nid
						)
					);
				}

				if (isset($order['updated_at']))
				{
					$label .= ' <span class="small">(' . $this->render_cell_datetime($entry, 'updated_at') . ')</span>';
				}
				else if (isset($order['size']))
				{
					$label .= ' <span class="small">(' . $this->render_cell_size($entry, 'size') . ')</span>';
				}
			}

			$img = $entry->thumbnail('$gallery')->to_element(array(

				'title' => $title,
				'alt' => $title

			));

			$path = $entry->path;

			$rc .= <<<EOT
<div class="thumbnailer-wrapper" style="width: {$size}px;">
<a href="$path" rel="lightbox[]">$img</a>
<div class="key">$key</div>
$label
</div>
EOT;

		}

		$rc .= '</td></tr>' . PHP_EOL;

		return $rc;
	}
}