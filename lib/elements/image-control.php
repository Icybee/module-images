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

use ICanBoogie\ActiveRecord\RecordNotFound;

use Brickrouge\Alert;
use Brickrouge\Button;
use Brickrouge\Element;

class ImageControl extends Element
{
	const MAX_FILE_SIZE = '#image-control-max-file-size';
	const MAX_FILE_SIZE_ALERT = '#image-control-max-file-size-alert';
	const ACCEPTED_EXTENSIONS = '#image-control-accepted-extensions';

	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes=[])
	{
		parent::__construct('div', $attributes + [

			Element::IS => 'ImageControl',

			self::MAX_FILE_SIZE => ini_get('upload_max_filesize') * 1024 * 1024,
			self::ACCEPTED_EXTENSIONS => '.gif .png .jpg .jpeg',

			'class' => 'widget-file-control'

		]);
	}

	protected function alter_dataset(array $dataset)
	{
		return parent::alter_dataset($dataset) + [

			'max-file-size' => $this[self::MAX_FILE_SIZE],
			'max-file-size-alert' => $this[self::MAX_FILE_SIZE_ALERT],
			'accepted-extensions' => $this[self::ACCEPTED_EXTENSIONS]

		];
	}

	public function offsetSet($attribute, $value)
	{
		if ($attribute == 'value' && $value)
		{
			$app = \ICanBoogie\app();

			try
			{
				$app->models['images'][$value];
			}
			catch (RecordNotFound $e)
			{
				$app->logger->error($e->getMessage());

				return;
			}
		}

		parent::offsetSet($attribute, $value);
	}

	public function render_inner_html()
	{
		$html = parent::render_inner_html();

		$input = new Element('input', [

			'type' => 'hidden',
			'name' => $this['name'],
			'value' => $this['value']

		]);

		$alert = new Alert($this->t("An error has occured"), [

			Alert::CONTEXT => Alert::CONTEXT_ERROR,
			Alert::UNDISMISSABLE => true

		]);

		$cancel = new Button("Cancel", [ 'class' => 'btn-danger' ]);

		$choose_a_file = $this->t("Choose a file");

		return <<<EOT
$html

<label class="btn">
	<i class="icon-file"></i> $choose_a_file <input type="file" />
</label>

<div class="progress like-input"><div class="progress-position" style="width:50%"><div class="progress-label">50%</div></div></div>
$cancel
$alert
$input
EOT;
	}
}
