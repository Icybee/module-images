# customization

PACKAGE_NAME = icybee/module-images
PACKAGE_VERSION = 3.0.0
COMPOSER_ENV = COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION)

# assets

JS_FILES = \
	lib/Operation/SaveOperation.js \
	lib/ImageControl.js \
	lib/ImageControlWithPreview.js \
	lib/AdjustImage.js \
	lib/AdjustThumbnail.js \
	lib/PopImage.js \
	lib/PopOrUploadImage.js

CSS_FILES = \
	lib/ImageControl.css \
	lib/ImageControlWithPreview.css \
	lib/AdjustImage.css \
	lib/AdjustThumbnail.css \
	lib/ImageUpload.css \
	lib/PopImage.css \
	lib/PopOrUploadImage.css

JS_COMPRESSOR = `which uglifyjs` $^ \
	--compress \
	--mangle \
	--screw-ie8 \
	--source-map $@.map
#JS_COMPRESSOR = cat $^ # uncomment to produce uncompressed files
JS_COMPRESSED = public/module.js
JS_UNCOMPRESSED = public/module-uncompressed.js

CSS_COMPRESSOR = curl -X POST -s --data-urlencode 'input@$^' http://cssminifier.com/raw
#CSS_COMPRESSOR = cat $^ # http://cssminifier.com/raw # uncomment to produce uncompressed files
CSS_COMPRESSED = public/module.css
CSS_UNCOMPRESSED = public/module-uncompressed.css

all: $(JS_COMPRESSED) $(JS_UNCOMPRESSED) $(CSS_COMPRESSED) $(CSS_UNCOMPRESSED)

$(JS_COMPRESSED): $(JS_UNCOMPRESSED)
	$(JS_COMPRESSOR) >$@

$(JS_UNCOMPRESSED): $(JS_FILES)
	cat $^ >$@

$(CSS_COMPRESSED): $(CSS_UNCOMPRESSED)
	$(CSS_COMPRESSOR) >$@

$(CSS_UNCOMPRESSED): $(CSS_FILES)
	cat $^ >$@

# do not edit the following lines

usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@$(COMPOSER_ENV) composer install

update:
	@$(COMPOSER_ENV) composer update

autoload: vendor
	@$(COMPOSER_ENV) composer dump-autoload

test: vendor
	@phpunit

test-coverage: vendor
	@mkdir -p build/coverage
	@phpunit --coverage-html build/coverage

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
