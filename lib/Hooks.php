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

use Brickrouge\AlterCSSClassNamesEvent;
use ICanBoogie\I18n;
use ICanBoogie\Event;
use ICanBoogie\Modules;
use ICanBoogie\Operation;

use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Group;
use Brickrouge\Text;

use Icybee\Modules\Contents\Block\ConfigBlock as ContentsConfigBlock;
use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Pages\PageRenderer;

class Hooks
{
	/*
	 * Events
	 */

	/**
	 * Finds the images associated with the records provided to the view.
	 *
	 * In order to avoid each record of the view to load its own image during rendering, we load
	 * them all and update the records.
	 *
	 * The method is canceled if there is only 3 records because bunch loading takes 3 database
	 * requests.
	 *
	 * @param \Icybee\Modules\Views\View\AlterRecordsEvent $event
	 * @param \Icybee\Modules\Contents\View $target
	 */
	static public function on_contents_view_alter_records(\Icybee\Modules\Views\View\AlterRecordsEvent $event, \Icybee\Modules\Contents\View $target)
	{
		$app = self::app();
		$records = &$event->records;

		if (count($records) < 3
		|| !$app->registry['images.inject.' . $target->module->flat_id])
		{
			return;
		}

		/* @var $image_model ImageModel */

		$image_model = $app->models['images'];
		$image_model->including_assigned_image($records);
	}

	/**
	 * Adds control for the image associated with the content.
	 *
	 * @param Event $event
	 * @param \Icybee\Modules\Nodes\Block\EditBlock $block
	 */
	static public function on_contents_editblock_alter_children(Event $event, \Icybee\Modules\Nodes\Block\EditBlock $block)
	{
		$registry = \ICanBoogie\app()->registry;
		$flat_id = $event->module->flat_id;
		$inject = $registry['images.inject.' . $flat_id];

		if (!$inject)
		{
			return;
		}

		$group = null;

		if (isset($event->attributes[Element::GROUPS]['contents']))
		{
			$group = 'contents';
		}

		$imageid = null;

		if ($block->record)
		{
			$imageid = $block->record->metas['image_id'];
		}

		$event->children['image_id'] = new PopOrUploadImage([

			Form::LABEL => $registry['images.inject.' . $flat_id . '.title'] ?: 'Image',
			Element::GROUP => $group,
			Element::REQUIRED => $registry['images.inject.' . $flat_id . '.required'],
			Element::DESCRIPTION => $registry['images.inject.' . $flat_id . '.description'],

			'value' => $imageid

		]);
	}

	/**
	 * Alters the config block of contents modules with controls for the associated image.
	 *
	 * @param Event $event
	 * @param \Icybee\Modules\Contents\Block\ConfigBlock $block
	 */
	static public function on_contents_configblock_alter_children(Event $event, ContentsConfigBlock $block)
	{
		$app = self::app();
		$app->document->css->add(DIR . 'public/admin.css');

		$module_id = $event->module->id;

		$views = [

			$module_id . '/home' => [

				'title' => $app->translate("Records home", [], [ 'scope' => $module_id ])

			],

			$module_id . '/list' => [

				'title' => $app->translate("Records list", [], [ 'scope' => $module_id ])

			],

			$module_id . '/view' => [

				'title' => $app->translate("Record detail", [], [ 'scope' => $module_id ])

			]
		];

		$thumbnails = [];

		foreach ($views as $view_id => $view)
		{
			$id = \ICanBoogie\normalize($view_id);

			$thumbnails["global[thumbnailer.versions][$id]"] = new \Brickrouge\Widget\PopThumbnailVersion([

				Element::GROUP => 'images__inject_thumbnails',
				Form::LABEL => $view['title'],// . ' <span class="small">(' . $id . ')</span>',
				Element::DESCRIPTION => 'Identifiant de la version&nbsp;: <q>' . $id . '</q>.'

			]);
		}

		$target_flat_id = $event->module->flat_id;

		$event->children = array_merge($event->children, [

			"global[images.inject][$target_flat_id]" => new Element(Element::TYPE_CHECKBOX, [

				Element::LABEL => 'image_inject_activate',
				Element::GROUP => 'images__inject_toggler'

			]),

			"global[images.inject][$target_flat_id.default]" => new PopImage([

				Form::LABEL => 'image_inject_default_image',
				Element::GROUP => 'images__inject'

			]),

			"global[images.inject][$target_flat_id.title]" => new Text([

				Group::LABEL => 'image_inject_control_title',
				Element::GROUP => 'images__inject',

				'placeholder' => 'Image'

			]),

			"global[images.inject][$target_flat_id.description]" => new Text([

				Group::LABEL => 'image_inject_control_description',
				Element::GROUP => 'images__inject'

			]),

			"global[images.inject][$target_flat_id.required]" => new Element(Element::TYPE_CHECKBOX, [

				Element::LABEL => 'image_inject_required',
				Element::GROUP => 'images__inject'

			])

		], $thumbnails);

		#
		# Listen to the block `alert_attributes` event to add our groups.
		#

		$event->attributes[Element::GROUPS] = array_merge($event->attributes[Element::GROUPS], [

			'images__inject_toggler' => [

				'title' => 'Associated image',
				'class' => 'group-toggler'

			],

			'images__inject' => [


			],

			'images__inject_thumbnails' => [

				'description' => 'Use the following elements to configure the
				thumbnails to create for the associated image. Each view provided by the
				module has its own thumbnail configuration:'

			]
		]);
	}

	static public function on_nodes_save(Event $event, \Icybee\Modules\Nodes\Operation\SaveOperation $operation)
	{
		$params = &$event->request->params;

		if (!isset($params['image_id']))
		{
			return;
		}

		$entry = $operation->module->model[$event->rc['key']];
		$imageid = $params['image_id'];

		$entry->metas['image_id'] = $imageid ? $imageid : null;
	}

	static public function before_contents_config(Event $event, \Icybee\Modules\Contents\ConfigOperation $operation)
	{
		if (!isset($event->request->params['global']['images.inject']))
		{
			return;
		}

		$module_flat_id = $operation->module->flat_id;
		$options = &$event->request->params['global']['images.inject'];

		$options += [

			$module_flat_id => false,
			$module_flat_id . '.required' => false,
			$module_flat_id . '.default' => null

		];

		$options[$module_flat_id] = filter_var($options[$module_flat_id], FILTER_VALIDATE_BOOLEAN);
		$options[$module_flat_id . '.required'] = filter_var($options[$module_flat_id . '.required'], FILTER_VALIDATE_BOOLEAN);
	}

	static public function textmark_images_reference(array $args, \Textmark_Parser $textmark, array $matches)
	{
		static $model;

		if (!$model)
		{
			$model = self::app()->models['images'];
		}

		$align = $matches[2];
		$alt = $matches[3];
		$id = $matches[4];

		# for shortcut links like ![this][].

		if (!$id)
		{
			$id = $alt;
		}

		/* @var $record Image */

		$record = $model->where('nid = ? OR slug = ? OR title = ?', (int) $id, $id, $id)->order('created_at DESC')->one;

		if (!$record)
		{
			self::app()->logger->debug('Invalid image: %id', [ 'id' => $matches[3] ]);

			return null;
		}

		$src = self::app()->url_for('files:show', $record);
		$w = $record->width;
		$h = $record->height;

		$light_src = null;

		if ($w > 600)
		{
			$w = 600;
			$h = null;

			$light_src = $src;

			$src = $record->thumbnail("w:$w")->url;
		}

		$params = [

			'src' => $src,
			'alt' => $alt,
			'width' => $w,
			'height' => $h

		];

		if ($align)
		{
			switch ($align)
			{
				case '<': $align = 'left'; break;
				case '=':
				case '|': $align = 'middle'; break;
				case '>': $align = 'right'; break;
			}

			$params['align'] = $align;
		}

		$rc = new Element('img', $params);

		if ($light_src)
		{
			$rc = '<a href="' . $light_src . '" rel="lightbox[]">' . $rc . '</a>';
		}

		return $rc;
	}

	/**
	 * Adds assets to support lightbox links.
	 *
	 * This function is a callback for the `Icybee\Modules\Pages\PageRenderer::render` event.
	 *
	 * @param PageRenderer\RenderEvent $event
	 * @param PageRenderer $target
	 */
	static public function on_page_renderer_render(PageRenderer\RenderEvent $event, PageRenderer $target)
	{
		if (strpos($event->html, 'rel="lightbox') === false)
		{
			return;
		}

		$document = $event->document;
		$document->css->add(DIR . 'public/slimbox.css');
		$document->js->add(DIR . 'public/slimbox.js');
	}

	static private $attached;

	/**
	 * @param AlterCSSClassNamesEvent $event
	 * @param Node $node
	 */
	static public function on_alter_css_class_names(AlterCSSClassNamesEvent $event, Node $node)
	{
		if (self::$attached === null)
		{
			$app = self::app();

			self::$attached = $app->models['registry/node']
			->select('targetid, value')
			->join('INNER JOIN {prefix}nodes ON(targetid = nid)')
			->where('(site_id = 0 OR site_id = ?) AND name = "image_id"', $app->site_id)
			->pairs;
		}

		if (empty(self::$attached[$node->nid]))
		{
			return;
		}

		$event->names['has-image'] = true;
	}

	/**
	 * Decorates `title` cell of the record with an associated image with an icon.
	 *
	 * @param \Icybee\Block\ManageBlock\AlterRenderedCellsEvent $event
	 * @param \Icybee\Modules\Contents\Block\ManageBlock $target
	 */
	static public function on_manageblock_alter_rendered_cells(\Icybee\Block\ManageBlock\AlterRenderedCellsEvent $event, \Icybee\Modules\Contents\Block\ManageBlock $target)
	{
		$app = self::app();

		if (!($app->registry["images.inject.{$target->module->flat_id}"]))
		{
			return;
		}

		/* @var $image_model ImageModel */

		$rendered_cells = &$event->rendered_cells;
		$image_model = $app->models['images'];
		$records = $image_model->including_assigned_image($event->records);

		foreach ($rendered_cells['title'] as $i => &$cell)
		{
			try
			{
				/* @var $image NodeRelation */

				$image = $records[$i]->image;

				if (!$image)
				{
					continue;
				}

				$cell = new ThumbnailDecorator($cell, $image->image);
			}
			catch (\Exception $e) {}
		}

		$app->document->css->add(DIR . 'public/slimbox.css');
		$app->document->js->add(DIR . 'public/slimbox.js');
	}

	/*
	 * Prototype methods
	 */

	/**
	 * Returns the image associated with the node.
	 *
	 * @param Node $node
	 *
	 * @return Image|null
	 */
	static public function prototype_get_image(Node $node)
	{
		$id = $node->metas['image_id'];

		if (!$id)
		{
			return null;
		}

		/* @var $image Image */

		$image = $node->model->models['images'][$id];

		return new NodeRelation($node, $image);
	}

	/**
	 * Callback for the `thumbnail()` method added to the active records of the "images" module.
	 *
	 * @param Image $record An active record of the "images" module.
	 * @param string|array $version The version used to create the thumbnail, or a number of
	 * options defined as CSS properties. e.g. 'w:300;h=200'.
	 * @param string $additional_options
	 *
	 * @return string The URL of the thumbnail.
	 */
	static public function prototype_thumbnail(Image $record, $version, $additional_options = null)
	{
		return new Thumbnail($record, $version, $additional_options);
	}

	/**
	 * Callback for the `thumbnail` getter added to the active records of the "images" module.
	 *
	 * The thumbnail is created using options of the 'primary' version.
	 *
	 * @param Image $record An active record of the "images" module.
	 *
	 * @return string The URL of the thumbnail.
	 */
	static public function prototype_get_thumbnail(Image $record)
	{
		return $record->thumbnail('primary');
	}

	/*
	 * Support
	 */

	/**
	 * @return \ICanBoogie\Core|\Icybee\Binding\CoreBindings
	 */
	static private function app()
	{
		return \ICanBoogie\app();
	}
}
