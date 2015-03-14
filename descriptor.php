<?php

namespace Icybee\Modules\Images;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return array
(
	Descriptor::ID => 'images',
	Descriptor::CATEGORY => 'resources',
	Descriptor::DESCRIPTION => 'Manages the images uploaded by the users of Icybee.',
	Descriptor::INHERITS => 'files',
	Descriptor::MODELS => array
	(
		'primary' => array
		(
			Model::EXTENDING => 'files',
			Model::SCHEMA => array
			(
				'fields' => array
				(
					'width' => array('integer', 'unsigned' => true),
					'height' => array('integer', 'unsigned' => true),
					'alt' => array('varchar', 80)
				)
			)
		)
	),

	Descriptor::NS => __NAMESPACE__,
	Descriptor::REQUIRES => array
	(
		'thumbnailer' => '1.0',
		'registry' => "2.x"
	),

	Descriptor::TITLE => 'Images',
	Descriptor::VERSION => '1.0'
);
