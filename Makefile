# customization

PACKAGE_NAME = icybee/module-images
PACKAGE_VERSION = 3.0.0

# assets

JS_FILES = \
	lib/operations/save.js \
	lib/elements/image-control.js \
	lib/elements/image-control-with-preview.js \
	lib/elements/adjust-image.js \
	lib/elements/adjust-thumbnail.js \
	lib/elements/pop-image.js \
	lib/elements/pop-or-upload-image.js

CSS_FILES = \
	lib/elements/image-control.css \
	lib/elements/image-control-with-preview.css \
	lib/elements/adjust-image.css \
	lib/elements/adjust-thumbnail.css \
	lib/elements/image-upload.css \
	lib/elements/pop-image.css \
	lib/elements/pop-or-upload-image.css

JS_COMPRESSOR = curl -X POST -s --data-urlencode 'js_code@$^' --data-urlencode 'utf8=1' http://marijnhaverbeke.nl/uglifyjs
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
	@composer install

update:
	@composer update

autoload: vendor
	@composer dump-autoload

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
	--title "$(PACKAGE_NAME) $(PACKAGE_VERSION)" \
	--template-theme "bootstrap"

clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock
	@rm -fR tests/repository/files
	@rm -fR tests/repository/thumbnailer
	@rm -fR tests/repository/tmp
