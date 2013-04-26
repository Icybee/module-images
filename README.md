# Images

The Images module (`images`) manages the images uploaded by the users of the
CMS [Icybee](http://icybee.org/).

The module provides the ability to attach an image to `Content` instances. For instance,
an image can be attached the a news in order to illustrate it.





## Requirement

The package requires PHP 5.3 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```json
{
	"minimum-stability": "dev",
	"require":
	{
		"icybee/module-images": "*"
	}
}
```

Note: The module is part of the modules required by Icybee.





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-images), its repository can be
cloned with the following command line:

	$ git clone git://github.com/Icybee/module-images.git images





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/Icybee/module-images.png?branch=master)](https://travis-ci.org/Icybee/module-images)





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## Associating images

Images can be associated with content records—such as a news—to illustrate it. An option to enable
the association is injected in all the modules extending the Contents module (`contents`). When the
option is enabled the user can specify the following things:

- That the association is required.
- The image to use by default if the association is not required.
- The title and description of the injected image control.

Additionnal controls allow the user to specify the thumbnail options to use for the different views
of the record, usually `home`, `list` and `view`.

These settings are store in the registry :

- `images.inject.<flat_target_module_id>`: (bool|null) `true` if enabled, undefined otherwise.
- `images.inject.<flat_target_module_id>.required`: (bool) `true` if the association is required,
false otherwise.
- `images.inject.<flat_target_module_id>.default`: (int) Identifier of a default image to use
when no image is associated to a record. This only apply when the association is not required.
- `images.inject.<flat_target_module_id>.title`: (string) The label of the image control injected
in the edit form of the record.
- `images.inject.<flat_target_module_id>.description`: (string) The description of the image
control injected in the edit form of the record.





### Edit control
	
The edit block of the target modules is altered to provide a control allowing the user to select
the image to associate with the record being edited.

The identifier of the selected image is recorded in the `image_id` meta property of the record.





### Obtaining the image associated with a record

The image associated with a record is obtained through the `image` magic property.

```php
<?php

$core->models['articles']->one->image;
```





## License

The package is licensed under the New BSD License - See the LICENSE file for details.