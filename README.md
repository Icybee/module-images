# Images
 
[![Packagist](https://img.shields.io/packagist/v/icybee/module-images.svg)](https://packagist.org/packages/icybee/module-images)
[![Build Status](https://img.shields.io/travis/Icybee/module-images.svg)](http://travis-ci.org/Icybee/module-images)
[![HHVM](https://img.shields.io/hhvm/Icybee/module-images.svg)](http://hhvm.h4cc.de/package/Icybee/module-images)
[![Code Quality](https://img.shields.io/scrutinizer/g/Icybee/module-images.svg)](https://scrutinizer-ci.com/g/Icybee/module-images)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-images.svg)](https://coveralls.io/r/Icybee/module-images)
[![Downloads](https://img.shields.io/packagist/dt/icybee/module-images.svg)](https://packagist.org/packages/icybee/module-images/stats)

The Images module (`images`) manages the images uploaded by the users of the
CMS [Icybee](http://icybee.org/).





## Rendering Image records

Image active records render as string:

```php
<?php

/* @var \ICanBoogie\Application $app */

$image = $app->models['images']->one;

echo $image;
```

Will produce something like:

```html
<img src="/repository/files/image/140-porte-verre-blanc.jpeg" alt="Porte verre" width="484" height="518" data-nid="140" />
```





## Thumbnails

The [icybee/module-thumbnailer][] package is used to create thumbnails from images. The
`thumbnail()` method is used to obtain a [Thumbnail][] instance which represent a thumbnail of an
image:

```php
<?php

/* @var \Icybee\Modules\Images\Image $image */

$thumbnail = $image->thumbnail([ 'w' => 64, 'h' => 64 ]);
# or
$thumbnail = $image->thumbnail('64x64');

echo $thumbnail->url; // /api/images/123/64x64
echo $thumbnail;      // <img src="/api/images/123/64x64" …
```





### Thumbnail versions

The following thumbnail versions are created when the module is installed:

- `$icon`: Represents an image or the image assigned to a record. It's a 24×24 image,
usually used in the `manage` view (the index of a module in the admin).

- `$icon-m`: A bigger icon usually used by the AdjustImage element to display available images
in a grid.

- `$popimage`: Represents a preview of an image in a PopImage element.

- `$popover`: Represents a preview of an image that appears as a popover when the cursor
hovers an `$icon` image.

- `$gallery`: Represents images when they are displayed in a gallery.

Creating a thumbnail with a version is very easy:

```php
<?php

/* @var \Icybee\Modules\Images\Image $image */

$thumbnail = $image->thumbnail('$popover');

echo $thumbnail->url;  // /api/images/abcd/thumbnails/$popover
echo $thumbnail;       // <img src="/api/images/abcd/thumbnails/$popover" …
```





## Assigning images to content records

The module provides to ability to assign images to content records—such as news or articles—to
illustrate them. The [Thumbnailer](https://github.com/Icybee/module-thumbnailer)
module is used to provide thumbnails through a fluent API.

```php
<?php

/* @var \ICanBoogie\Application $app */

echo $app->models['images']->one->thumbnail;
echo $app->models['images']->one->thumbnail('news-list');

echo $app->models['news']->one->image->thumbnail('news-list');
# or
echo $app->models['news']->one->image->thumbnail(':list');

echo $app->models['news']->one->image->thumbnail('news-view');
# or
echo $app->models['news']->one->image->thumbnail;
```





### Configuring the assigning

An option to enable the association is injected in all the modules extending the [Contents](https://github.com/Icybee/module-contents) module.
When the option is enabled the following things can be specified:

- Whether or not the assigning is required.
- The image to use by default if the association is not required.
- The title and description of the injected image control.

These settings are stored in the global registry:

- `images.inject.<flat_module_id>`: (bool|null) `true` if enabled, undefined otherwise.
- `images.inject.<flat_module_id>.required`: (bool) `true` if the association is required,
false otherwise.
- `images.inject.<flat_module_id>.default`: (int) Identifier of a default image to use
when no image is associated to a record. This only apply when the association is not required.
- `images.inject.<flat_module_id>.title`: (string) The label of the image control injected
in the edit form of the record.
- `images.inject.<flat_module_id>.description`: (string) The description of the image
control injected in the edit form of the record.

Additional controls specify the thumbnail options to use for the different views of the record,
usually `home`, `list` and `view`. The thumbnail version name is created according to the following
pattern: `<module>-<view>`, where `<module>` is the normalized identifier of the module, and
`<view>` is the normalized identifier of the view.





### Edit control

The edit block of the target modules is altered to provide a control allowing the user to select
the image to associate with the record being edited.

The identifier of the selected image is recorded in the `image_id` meta property of the record.





### Obtaining the image associated with a record

The image associated with a record is obtained through the `image` magic property:

```php
<?php

/* @var \ICanBoogie\Application $app */

$app->models['articles']->one->image;
```

Note that it's not an `Image` instance that is obtained but a `NodeRelation` instance. Because
all calls are forwarded, the `NodeRelation` instance can be used just like an `Image` instance,
although _set_ throws a [PropertyNotWritable](http://icanboogie.org/docs/class-ICanBoogie.PropertyNotWritable.html)
exception.

The `NodeRelation` instance makes it possible to use short thumbnail versions. For instance, one
can use ":list" instead of "article-list" to obtain the thumbnail to use in a _list_ view of
articles:

```php
<?php 

/* @var \ICanBoogie\Application $app */

$app->models['articles']->one->image->thumbnail(':list');
```

The magic property `thumbnail` returns the _view_ thumbnail:

```php
<?php 

/* @var \ICanBoogie\Application $app */

$app->models['articles']->one->image->thumbnail(':view');
// or
$app->models['articles']->one->image->thumbnail;
```





## Thumbnail decorator

Components can be easily decorated with a thumbnail using a `ThumbnailDecorator` instance:

```php
<?php

use Icybee\Modules\Images\ThumbnailDecorator;

echo new ThumbnailDecorator($record->title, $record->image);
```

The previous code will produce something like:

```html
<a href="/repository/files/image/140-porte-verre-blanc.jpeg" rel="lightbox[thumbnail-decorator]"><img width="24" height="24" data-popover-image="/api/images/140/thumbnails/$popover" class="thumbnail thumbnail--icon" alt="" src="/api/images/140/thumbnails/$icon"></a> My record title
```





## Event hooks

The following event hooks are used:

- `Icybee\Modules\Views\View::alter_records`: Include the assigned images to the records, if any.





----------





## Requirement

The package requires PHP 5.6 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```
$ composer require icybee/module-images
```

**Note:** This module is part of the modules required by [Icybee](http://icybee.org/).





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-images), its repository can be
cloned with the following command line:

	$ git clone git://github.com/Icybee/module-images.git images





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/Icybee/module-images.svg)](http://travis-ci.org/Icybee/module-images)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-images.svg)](https://coveralls.io/r/Icybee/module-images)





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## License

The package is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[icybee/module-thumbnailer]: https://github.com/Icybee/module-thumbnailer
[Thumbnail]: http://api.icybee.org/class-Icybee.Modules.Images.Thumbnail.html
