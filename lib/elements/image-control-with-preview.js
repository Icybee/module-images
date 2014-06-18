define('icybee/images/image-control-with-preview', [ 'icybee/images/image-control' ], function(ImageControl) {

	return new Class({

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options) {

			var control

			this.element = el = document.id(el)
			this.imgElement = el.getElement('img')
			this.control = control = el.getElement(Brickrouge.WIDGET_SELECTOR).get('widget')

			this.control.addEvent('change', function(value) {

				this.setValue(value)

			}.bind(this))

			el.addEventListener('dragover', function(ev) { control.onDragover(ev) }, false)
			el.addEventListener('dragleave', function(ev) { control.onDragleave(ev) }, false)
			el.addEventListener('drop', function(ev) { control.onDrop(ev) }, false)
		},

		setValue: function(value) {

			this.imgElement.src = "/api/images/" + value + "/thumbnails/" + this.options.thumbnailVersion + "?" + (new Date().getTime()).toString(16)

		},

		getValue: function(value) {

			return this.control.getValue()

		}

	})

})

/**
 * Register Brickrouge widget "ImageControlWithPreview"
 */
require([ 'icybee/images/image-control-with-preview' ], function(ImageControlWithPreview) {

	Brickrouge.Widget.ImageControlWithPreview = ImageControlWithPreview

})
;