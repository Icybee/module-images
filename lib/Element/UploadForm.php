<?php

namespace Icybee\Modules\Images\Element;

use Brickrouge\Element;
use Brickrouge\File;
use Brickrouge\Form;
use Brickrouge\Group;
use Brickrouge\Document;
use Icybee\Modules\Nodes\Node;

class UploadForm extends Form
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(__DIR__ . '/UploadForm.js', 200);

	}

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes + [

			Form::RENDERER => Form\GroupRenderer::class,
			Element::CHILDREN => $this->create_children($attributes),

			'class' => 'form-primary'

		]);
	}

	/**
	 * @param array $attributes
	 *
	 * @return Element[]
	 */
	protected function create_children(array $attributes)
	{
		return [

			'files' => new File([

				Group::LABEL => "file",
				Element::REQUIRED => true,
				Element::IS => 'UploadFiles',

				'data-upload-url' => '/api/images'

			]),

			'options' => new Group([

				Element::LEGEND => "Options",
				Element::CHILDREN => [

					Node::IS_ONLINE => new Element(Element::TYPE_CHECKBOX, [

						Element::LABEL => 'is_online',
						Element::DESCRIPTION => 'is_online',
						Element::GROUP => 'visibility'

					]),

					Node::UID => $this->get_control__user()

				]

			])

		];
	}

	/**
	 * Returns the control for the user of the node.
	 *
	 * @return Element|null
	 */
	protected function get_control__user()
	{
		$app = $this->app;
/*
		if (!$app->user->has_permission(\ICanBoogie\Module::PERMISSION_ADMINISTER, $this->module))
		{
			return null;
		}
*/
		$users = $app->models['users']->select('uid, username')->order('username')->pairs;

		if (count($users) < 2)
		{
			return null;
		}

		return new Element('select', [

			Group::LABEL => 'User',
			Element::OPTIONS => [ null => '' ] + $users,
			Element::REQUIRED => true,
			Element::DEFAULT_VALUE => $app->user->uid,
			Element::GROUP => 'admin',
			Element::DESCRIPTION => 'user'

		]);
	}
}
