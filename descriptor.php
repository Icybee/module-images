<?php

namespace Icybee\Modules\Images;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::ID => 'images',
	Descriptor::CATEGORY => 'resources',
	Descriptor::DESCRIPTION => "Manages the images uploaded by the users of Icybee.",
	Descriptor::INHERITS => 'files',
	Descriptor::MODELS => [

		'primary' => [

			Model::EXTENDING => 'files',
			Model::SCHEMA => [

				'width' => [ 'integer', 'unsigned' => true ],
				'height' => [ 'integer', 'unsigned' => true ],
				'alt' => [ 'varchar', 80 ]

			]
		]
	],

	Descriptor::NS => __NAMESPACE__,
	Descriptor::REQUIRES => [ 'thumbnailer', 'registry' ],
	Descriptor::TITLE => "Images"

];
