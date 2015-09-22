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

use Brickrouge\Document;
use Brickrouge\Element;

class ImageControlWithPreview extends Element
{
	const CONTROL = "#preview-decorator-control";
	const THUMBNAIL_VERSION = "#preview-decorator-thumbnail-version";

	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/module.css');
		$document->js->add(DIR . 'public/module.js');
	}

	public function __construct(array $attributes = [])
	{
		parent::__construct('div', $attributes + [

			Element::IS => 'ImageControlWithPreview',

			self::THUMBNAIL_VERSION => '$popimage',

			'class' => 'widget-image-control-with-preview'

		]);
	}

	protected function alter_dataset(array $dataset)
	{
		return parent::alter_dataset($dataset) + [

			'thumbnail-version' => $this[self::THUMBNAIL_VERSION]

		];
	}

	protected function render_inner_html()
	{
		$control = $this[self::CONTROL];
		$control['name'] = $this['name'];
		$control['value'] = $value = $this['value'];

		$src = '';

		if ($value)
		{
			$src = "/api/images/$value/thumbnails/" . $this[self::THUMBNAIL_VERSION];
		}

		return <<<EOT
<div class="preview-wrapper">
	<img src="$src" alt="" />
	<button data-dismiss="value" class="close" type="button">×</button>
</div>
<div class="control-wrapper">$control</div>
EOT;
	}
}
