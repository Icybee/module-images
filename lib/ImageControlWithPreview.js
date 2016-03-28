define('icybee/images/image-control-with-preview', [

	'brickrouge'

], function(Brickrouge) {

	return new Class({

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options) {

			var control

			this.element = el = document.id(el)
			this.imgElement = el.getElement('img')
			this.control = control = Brickrouge.from(el.getElement('[' + Brickrouge.IS_ATTRIBUTE + ']'))

			this.control.addEvent('change', this.rethinkState.bind(this))

			el.addEventListener('dragover', function(ev) { control.onDragover(ev) }, false)
			el.addEventListener('dragleave', function(ev) { control.onDragleave(ev) }, false)
			el.addEventListener('drop', function(ev) { control.onDrop(ev) }, false)
			el.addEvent('click:relay([data-dismiss="value"])', function() {

				this.setValue(null)

			}.bind(this))

			this.rethinkState()
		},

		rethinkState: function() {

			var value = this.getValue()
			, src = value|0
			? "/api/images/" + value + "/thumbnails/" + this.options.thumbnailVersion + "?no-cache=" + (new Date().getTime()).toString(16)
			: ''

			this.imgElement.src = src
			this.element[src ? 'removeClass' : 'addClass']('empty')
		},

		setValue: function(value) {

			this.control.setValue(value)
			this.rethinkState()

		},

		getValue: function(value) {

			return this.control.getValue()

		}

	})

})

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('ImageControlWithPreview', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/image-control-with-preview')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
