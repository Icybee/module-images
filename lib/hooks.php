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

use ICanBoogie\Debug;
use ICanBoogie\I18n;
use ICanBoogie\Event;
use ICanBoogie\Events;
use ICanBoogie\Modules;
use ICanBoogie\Modules\Thumbnailer\PopThumbnailVersion;
use ICanBoogie\Operation;

use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Group;
use Brickrouge\Text;

use Icybee\Modules\Contents\ConfigBlock as ContentsConfigBlock;
use Icybee\Modules\Nodes\Node;
use Icybee\Modules\Pages\PageController;
use Icybee\Modules\Views\ActiveRecordProvider\AlterResultEvent;

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
	 * TODO-20120713: Use an event to load images when the first `image` property is accessed.
	 *
	 * @param AlterResultEvent $event
	 * @param \Icybee\Modules\Contents\ViewProvider $provider
	 */
	static public function on_contents_provider_alter_result(AlterResultEvent $event, \Icybee\Modules\Contents\ViewProvider $provider)
	{
		global $core;

		$result = $event->result;

		if (!is_array($result) || count($result) < 3 || !(current($result) instanceof \Icybee\Modules\Contents\Content)
		|| !$core->registry['images.inject.' . $event->module->flat_id])
		{
			return;
		}

		$node_keys = array();

		foreach ($result as $node)
		{
			$node_keys[] = $node->nid;
		}

		$image_keys = $core->models['registry/node']
		->select('targetid, value')
		->where(array('targetid' => $node_keys, 'name' => 'image_id'))
		->where('value + 0 != 0')
		->pairs;

		if (!$image_keys)
		{
			return;
		}

		$images = $core->models['images']->find($image_keys);

		foreach ($result as $node)
		{
			$nid = $node->nid;

			if (empty($image_keys[$nid]))
			{
				continue;
			}

			$imageid = $image_keys[$nid];
			$image = $images[$imageid];

			$node->image = new NodeRelation($node, $image);
		}
	}

	/**
	 * Adds control for the image associated with the content.
	 *
	 * @param Event $event
	 * @param \Icybee\Modules\Nodes\EditBlock $block
	 */
	static public function on_contents_editblock_alter_children(Event $event, \Icybee\Modules\Nodes\EditBlock $block)
	{
		global $core;

		$flat_id = $event->module->flat_id;
		$inject = $core->registry['images.inject.' . $flat_id];

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

		$event->children['image_id'] = new PopOrUploadImage
		(
			array
			(
				Form::LABEL => $core->registry['images.inject.' . $flat_id . '.title'] ?: 'Image',
				Element::GROUP => $group,
				Element::REQUIRED => $core->registry['images.inject.' . $flat_id . '.required'],
				Element::DESCRIPTION => $core->registry['images.inject.' . $flat_id . '.description'],

				'value' => $imageid
			)
		);
	}

	/**
	 * Alters the config block of contents modules with controls for the associated image.
	 *
	 * @param Event $event
	 * @param \Icybee\Modules\Contents\ConfigBlock $block
	 */
	static public function on_contents_configblock_alter_children(Event $event, ContentsConfigBlock $block)
	{
		global $core;

		$core->document->css->add(DIR . 'public/admin.css');

		$module_id = $event->module->id;

		$views = array
		(
			$module_id . '/home' => array
			(
				'title' => I18n\t("Records home", array(), array('scope' => $module_id))
			),

			$module_id . '/list' => array
			(
				'title' => I18n\t("Records list", array(), array('scope' => $module_id))
			),

			$module_id . '/view' => array
			(
				'title' => I18n\t("Record detail", array(), array('scope' => $module_id))
			)
		);

		$thumbnails = array();

		foreach ($views as $view_id => $view)
		{
			$id = \ICanBoogie\normalize($view_id);

			$thumbnails["global[thumbnailer.versions][$id]"] = new PopThumbnailVersion
			(
				array
				(
					Element::GROUP => 'images__inject_thumbnails',
					Form::LABEL => $view['title'],// . ' <span class="small">(' . $id . ')</span>',
					Element::DESCRIPTION => 'Identifiant de la version&nbsp;: <q>' . $id . '</q>.'
				)
			);
		}

		$target_flat_id = $event->module->flat_id;

		$event->children = array_merge
		(
			$event->children, array
			(
				"global[images.inject][$target_flat_id]" => new Element
				(
					Element::TYPE_CHECKBOX, array
					(
						Element::LABEL => 'image_inject_activate',
						Element::GROUP => 'images__inject_toggler'
					)
				),

				"global[images.inject][$target_flat_id.default]" => new PopImage
				(
					array
					(
						Form::LABEL => 'image_inject_default_image',
						Element::GROUP => 'images__inject'
					)
				),

				"global[images.inject][$target_flat_id.title]" => new Text
				(
					array
					(
						Group::LABEL => 'image_inject_control_title',
						Element::GROUP => 'images__inject',

						'placeholder' => 'Image'
					)
				),

				"global[images.inject][$target_flat_id.description]" => new Text
				(
					array
					(
						Group::LABEL => 'image_inject_control_description',
						Element::GROUP => 'images__inject'
					)
				),

				"global[images.inject][$target_flat_id.required]" => new Element
				(
					Element::TYPE_CHECKBOX, array
					(
						Element::LABEL => 'image_inject_required',
						Element::GROUP => 'images__inject'
					)
				)
			),

			$thumbnails
		);

		#
		# Listen to the block `alert_attributes` event to add our groups.
		#

		$event->attributes[Element::GROUPS] = array_merge
		(
			$event->attributes[Element::GROUPS], array
			(
				'images__inject_toggler' => array
				(
					'title' => 'Associated image',
					'class' => 'group-toggler'
				),

				'images__inject' => array
				(

				),

				'images__inject_thumbnails' => array
				(
					'description' => 'Use the following elements to configure the
					thumbnails to create for the associated image. Each view provided by the
					module has its own thumbnail configuration:'
				)
			)
		);
	}

	static public function on_nodes_save(Event $event, \Icybee\Modules\Nodes\SaveOperation $operation)
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

		$options += array
		(
			$module_flat_id => false,
			$module_flat_id . '.required' => false,
			$module_flat_id . '.default' => null
		);

		$options[$module_flat_id] = filter_var($options[$module_flat_id], FILTER_VALIDATE_BOOLEAN);
		$options[$module_flat_id . '.required'] = filter_var($options[$module_flat_id . '.required'], FILTER_VALIDATE_BOOLEAN);
	}

	static public function textmark_images_reference(array $args, \Textmark_Parser $textmark, array $matches)
	{
		global $core;
		static $model;

		if (!$model)
		{
			$model = $core->models['images'];
		}

		$align = $matches[2];
		$alt = $matches[3];
		$id = $matches[4];

		# for shortcut links like ![this][].

		if (!$id)
		{
			$id = $alt;
		}

		$record = $model->where('nid = ? OR slug = ? OR title = ?', (int) $id, $id, $id)->order('created DESC')->one;

		if (!$record)
		{
			$matches[2] = $matches[3];
			$matches[3] = $matches[4];

			trigger_error('should call standard one !');

			//return parent::_doImages_reference_callback($matches);

			return;
		}

		$src = $record->path;
		$w = $record->width;
		$h = $record->height;

		$light_src = null;

		if ($w > 600)
		{
			$w = 600;
			$h = null;

			$light_src = $src;

			$src = $record->thumbnail("w:$w;method:fixed-width;quality:80")->url;
		}

		$params = array
		(
			'src' => $src,
			'alt' => $alt,
			'width' => $w,
			'height' => $h
		);

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
	 * This function is a callback for the `Icybee\Modules\Pages\PageController::render` event.
	 *
	 * @param Event $event
	 */
	static public function on_page_controller_render(PageController\RenderEvent $event, PageController $target)
	{
		global $core;

		if (strpos($event->html, 'rel="lightbox') === false)
		{
			return;
		}

		$document = $core->document;
		$document->css->add(DIR . 'public/slimbox.css');
		$document->js->add(DIR . 'public/slimbox.js');
	}

	static private $attached;

	static public function on_alter_css_class_names(\Brickrouge\AlterCSSClassNamesEvent $event, \Icybee\Modules\Nodes\Node $node)
	{
		global $core;

		if (self::$attached === null)
		{
			self::$attached = $core->models['registry/node']
			->select('targetid, value')
			->joins('INNER JOIN {prefix}nodes ON(targetid = nid)')
			->where('(siteid = 0 OR siteid = ?) AND name = "image_id"', $core->site_id)
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
	 * @param \Icybee\ManageBlock\AlterRenderedCellsEvent $event
	 * @param \Icybee\Modules\Contents\ManageBlock $target
	 */
	static public function on_manageblock_alter_rendered_cells(\Icybee\ManageBlock\AlterRenderedCellsEvent $event, \Icybee\Modules\Contents\ManageBlock $target)
	{
		global $core;

		if (!($core->registry["images.inject.{$target->module->flat_id}"]))
		{
			return;
		}

		$rendered_cells = &$event->rendered_cells;
		$records = $core->models['images']->including_assigned_image($event->records);

		foreach ($rendered_cells['title'] as $i => &$cell)
		{
			try
			{
				$image = $records[$i]->image;

				if (!$image)
				{
					continue;
				}

				$cell = new ThumbnailDecorator($cell, $image);
			}
			catch (\Exception $e) {}
		}

		$core->document->css->add(DIR . 'public/slimbox.css');
		$core->document->js->add(DIR . 'public/slimbox.js');
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
	static public function prototype_get_image(\Icybee\Modules\Nodes\Node $node)
	{
		global $core;

		$id = $node->metas['image_id'];

		if (!$id)
		{
			return;
		}

		$image = $core->models['images'][$id];

		return new NodeRelation($node, $image);
	}
}