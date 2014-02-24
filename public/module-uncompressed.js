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
		var capture = url.match(/\/api\/images\/(\d+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/)
		, nid = capture[1]
		, width = capture[3] || null
		, height = capture[4] || null
		, method = capture[6]
		, qs = url.indexOf('?')
		, options = {}

		if (qs)
		{
			options = url.substring(qs + 1).parseQueryString()
		}

		if (nid)
		{
			options.nid = nid
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

	return new Class({

		Extends: Brickrouge.Widget.PopNode,

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options)
		{
			this.parent(el, options)

			var img = this.element.getElement('img')

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

		change: function(ev)
		{
			this.setValue(ev.selected.get('data-nid'))
		},

		formatValue: function(value)
		{
			var img = this.img

			if (!value) return ''

			img.src = Request.API.encode('images/' + value + '/thumbnails/' + this.options.thumbnailVersion + '?_r=' + Date.now())

			return img
		}
	})

}) ();Brickrouge.Widget.PopOrUploadImage = (function() {

	var UPLOAD_MODE = 'upload-mode'
	, UPLOAD_MODE_CREATE = 'create'
	, UPLOAD_MODE_UPDATE = 'update'

	, constructor = new Class({

		initialize: function(el, options) {

			this.element = el = document.id(el)

			var pop = el.getElement('.widget-pop-image').get('widget')
			, upload = el.getElement('.widget-file').get('widget')
			, nid = null

			if (upload.options.uploadMode == UPLOAD_MODE_UPDATE)
			{
				nid = pop.getValue()
			}

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

					pop.setValue(response.rc.key)

				}
			})
		}
	})

	constructor.UPLOAD_MODE = UPLOAD_MODE
	constructor.UPLOAD_MODE_CREATE = UPLOAD_MODE_CREATE
	constructor.UPLOAD_MODE_UPDATE = UPLOAD_MODE_UPDATE

	return constructor

} ());