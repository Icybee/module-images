# customization

PACKAGE_NAME = icybee/module-images
PACKAGE_VERSION = 4.0
PHPUNIT_VERSION = phpunit-5.7.phar
PHPUNIT_FILENAME = build/$(PHPUNIT_VERSION)
PHPUNIT = php $(PHPUNIT_FILENAME)

# assets

JS_FILES = \
	lib/Operation/SaveOperation.js \
	lib/ImageControl.js \
	lib/ImageControlWithPreview.js \
	lib/AdjustImage.js \
	lib/AdjustThumbnail.js \
	lib/PopImage.js \
	lib/PopOrUploadImage.js

CSS_FILES = $(shell find ./lib -name *.scss)

JS_COMPRESSOR = `which uglifyjs` $^ \
	--compress \
	--mangle \
	--screw-ie8 \
	--source-map $@.map
#JS_COMPRESSOR = cat $^ # uncomment to produce uncompressed files
JS_COMPRESSED = public/module.js
JS_UNCOMPRESSED = public/module-uncompressed.js

CSS_COMPILER = `which sass`
CSS_COMPILER_OPTIONS = --style compressed   # comment to disable compression
CSS_COMPRESSED = public/module.css

all: $(JS_COMPRESSED) $(JS_UNCOMPRESSED) $(CSS_COMPRESSED) $(CSS_UNCOMPRESSED)

$(JS_COMPRESSED): $(JS_UNCOMPRESSED)
	$(JS_COMPRESSOR) >$@

$(JS_UNCOMPRESSED): $(JS_FILES)
	cat $^ >$@

$(CSS_COMPRESSED): $(CSS_FILES)
	$(CSS_COMPILER) $(CSS_COMPILER_OPTIONS) lib/module.scss:$@

# do not edit the following lines

usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer install

update:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer update

autoload: vendor
	@composer dump-autoload

$(PHPUNIT_FILENAME):
	mkdir -p build
	wget https://phar.phpunit.de/$(PHPUNIT_VERSION) -O $(PHPUNIT_FILENAME)

test: test-dependencies
	@$(PHPUNIT)

test-dependencies: vendor $(PHPUNIT_FILENAME)

test-coverage: test-dependencies
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html ../build/coverage

test-coveralls: test-dependencies
	@mkdir -p build/logs
	COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer require satooshi/php-coveralls
	@$(PHPUNIT) --coverage-clover ../build/logs/clover.xml
	php vendor/bin/coveralls -v

doc: vendor
	@mkdir -p build/docs
	@apigen generate \
	--source lib \
	--destination build/docs/ \
	--title "$(PACKAGE_NAME) v$(PACKAGE_VERSION)" \
	--template-theme "bootstrap"

clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock
	@rm -fR tests/repository/files
	@rm -fR tests/repository/thumbnailer
	@rm -fR tests/repository/tmp

.PHONY: all autoload doc clean test test-coverage test-coveralls test-dependencies update
