Brickrouge.Widget.PopImage = (function() {

	var paths = []

	return new Class({

		Extends: Brickrouge.Widget.PopNode,

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options)
		{
			this.parent(el, options)

			var nid = this.getValue()
			, img = this.element.getElement('img')

			if (nid)
			{
				paths[nid] = img.get('data-path')
			}

			img.addEvent('load', function(ev) {

				var img = ev.target

				img.set('width', img.naturalWidth)
				img.set('height', img.naturalHeight)

				if (this.popover)
				{
					this.popover.reposition()
				}

			}.bind(this))

			this.img = img
		},

		/**
		 * Updates the `paths` variable with the identifier and path of the selected image.
		 */
		change: function(ev)
		{
			var selected = ev.selected
			, nid = selected.get('data-nid')
			, path = selected.get('data-path')

			paths[nid] = path

			this.setValue(ev.selected.get('data-nid'))
		},

		/**
		 * Because the widget has no way of knowing the path of an image with only its identifier,
		 * the `path` parameter may be used to provide this path. It is required if the path is
		 * not referenced in `paths`.
		 */
		setValue: function(value, path)
		{
			if (value && path)
			{
				paths[value] = path
			}

			return this.parent(value)
		},

		/**
		 * The path of the image is required to create a correct URL. It must be defined in the
		 * `paths` variable.
		 */
		formatValue: function(value)
		{
			var img = this.img

			if (!value) return ''

			img.src = paths[value] + '/thumbnails/' + this.options.thumbnailVersion

			return img
		}
	})

}) ();