# Images [![Build Status](https://travis-ci.org/Icybee/module-images.png?branch=master)](https://travis-ci.org/Icybee/module-images)

The Images module (`images`) manages the images uploaded by the users of the
CMS [Icybee](http://icybee.org/).

The module provides the ability to attach images to `Content` instances. For instance,
an image can be attached to a news to illustrate it. The [Thumbnailer](https://github.com/Icybee/module-thumbnailer)
module is used to provide thumbnails through a fluent API.

```php
<?php

echo $core->models['images']->one->thumbnail;
echo $core->models['images']->one->thumbnail('news-list');

echo $core->models['news']->one->image->thumbnail('news-list');
# or
echo $core->models['news']->one->image->thumbnail(':list');

echo $core->models['news']->one->image->thumbnail('news-view');
# or
echo $core->models['news']->one->image->thumbnail;
```





## Thumbnail versions

When the module is installed it creates three thumbnailer versions:

- `$icon` is used to represent an image or the image associated to a record. It's a 64×64 image,
usually used in the `manage` view (the index of a module in the admin).

- `$popover` is used to represent a preview of an image that appears as a popover when the cursor
hovers an `$icon` image.

- `$gallery` is used to represent images when they are displayed in a gallery.






## Associating images

Images can be associated to content records—such as news—to illustrate them. An option to enable
the association is injected in all the modules extending the [Contents](https://github.com/Icybee/module-contents) module.
When the option is enabled the user can specify the following things:

- That the association is required.
- The image to use by default if the association is not required.
- The title and description of the injected image control.

Additional controls allow the user to specify the thumbnail options to use for the different views
of the record, usually `home`, `list` and `view`.

These settings are store in the registry :

- `images.inject.<flat_module_id>`: (bool|null) `true` if enabled, undefined otherwise.
- `images.inject.<flat_module_id>.required`: (bool) `true` if the association is required,
false otherwise.
- `images.inject.<flat_module_id>.default`: (int) Identifier of a default image to use
when no image is associated to a record. This only apply when the association is not required.
- `images.inject.<flat_module_id>.title`: (string) The label of the image control injected
in the edit form of the record.
- `images.inject.<flat_module_id>.description`: (string) The description of the image
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

Note that it's not an `Image` instance that is obtained but a `NodeRelation` instance. Because
all calls are forwarded, the `NodeRelation` instance can be used just like an `Image` instance,
although _set_ throws a [PropertyNotWritable](http://icanboogie.org/docs/class-ICanBoogie.PropertyNotWritable.html)
exception.

The `NodeRelation` instance makes it possible to use short thumbnail versions. For instance, one can
use ":list" instead of "article-list" to obtain the _list_ thumbnail of an article:

```php
<?php 

$core->models['articles']->one->image->thumbnail(':list');
```

The magic property `thumbnail` returns the _view_ thumbnail:

```php
<?php 

$core->models['articles']->one->image->thumbnail(':view');
// or
$core->models['articles']->one->image->thumbnail;
```





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

Note: This module is part of the modules required by [Icybee](http://icybee.org/).





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





## License

The package is licensed under the New BSD License - See the LICENSE file for details.