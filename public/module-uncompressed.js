Brickrouge.Widget.AdjustImage = new Class({

	Extends: Brickrouge.Widget.AdjustNode

});Brickrouge.Widget.AdjustThumbnail = new Class({

	Implements: [ Events ],

	initialize: function(el, options)
	{
		this.element = el = document.id(el)
		this.control = el.getElement('input[type="hidden"]')
		this.thumbnailOptions = el.getElement('.widget-adjust-thumbnail-options').get('widget')
		this.image = el.getElement('.widget-adjust-image').get('widget')

		this.thumbnailOptions.addEvent('change', this.onChange.bind(this))
		this.image.addEvent('change', this.onChange.bind(this))

		el.getFirst('.more').addEvent('click', function(ev) {

			this.element.toggleClass('unfold-thumbnail')
			this.fireEvent('adjust', { target: this.element, widget: this })

		}.bind(this))
	},

	decodeOptions: function(url)
	{
		var capture = url.match(/\/api\/images\/([0-9a-z]{8})-([0-9a-z]{48})(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/)
		, hexdec = capture[1]
		, width = capture[4] || null
		, height = capture[5] || null
		, method = capture[7]
		, qs = url.indexOf('?')
		, options = {}

		if (qs)
		{
			options = url.substring(qs + 1).parseQueryString()
		}

		if (hexdec)
		{
			options.nid = parseInt(hexdec, 16)
		}

		options.width = width
		options.height = height
		options.method = method

		return options
	},

	setValue: function(value)
	{
		var options = { nid: null }
		, el = null

		if (typeOf(value) == 'element')
		{
			el = value
			value = el.get('src')
		}

		if (typeOf(value) == 'string' && value.indexOf('/api/') !== -1)
		{
			options = this.decodeOptions(value)
		}
		else if (typeOf(value) == 'object')
		{
			options = value
		}
		else
		{
			options.nid = value
		}

		if (el)
		{
			options.width = el.get('width') || options.width
			options.height = el.get('height') || options.height
		}

		this.image.setValue(options.nid)
		this.thumbnailOptions.setValue(options)
	},

	getValue: function()
	{
		var selected = this.image.getSelected()
		, options = this.thumbnailOptions.getValue()
		, url = null
		, thumbnail = null

		if (selected)
		{
			url = selected.get('data-path')

			try
			{
				thumbnail = new ICanBoogie.Modules.Thumbnailer.Thumbnail(url, options)
				url = thumbnail.toString()
			}
			catch (e)
			{
				if (console) console.log(e)
			}
		}

		return url
	},

	onChange: function(ev)
	{
		var selected = this.image.getSelected()

		this.fireEvent('change', {

			target: this,
			url: this.getValue(),
			nid: selected ? selected.get('data-nid') : null,
			selected: selected,
			options: this.thumbnailOptions.getValue()

		})
	}
});Brickrouge.Widget.PopImage = (function() {

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

}) ();Brickrouge.Widget.PopOrUploadImage = new Class({

	initialize: function(el, options) {

		this.element = el = document.id(el)

		var pop = el.getElement('.widget-pop-image').get('widget')
		, upload = el.getElement('.widget-file').get('widget')
		, nid = null

		upload.addEvents({

			prepare: function(ev) {

				var data = ev.data

				data.append('title', ev.file.name)
				data.append('is_online', true)

				if (nid)
				{
					data.append(ICanBoogie.Operation.KEY, nid)
				}
			},

			success: function(response) {

				nid = response.rc.key

				pop.setValue(response.rc.key, response.rc.path)

			}
		})
	}
});