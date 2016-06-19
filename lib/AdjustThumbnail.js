define('icybee/images/adjust-thumbnail', [

	'brickrouge'

], (Brickrouge) => {

	return new Class({

		Implements: [ Events ],

		initialize: function(el, options)
		{
			this.element = el
			this.control = el.querySelector('input[type="hidden"]')
			this.thumbnailOptions = Brickrouge.from(el.querySelector('.widget-adjust-thumbnail-options'))
			this.thumbnailOptions.addEvent('change', this.onChange.bind(this))

			this.adjustImage = Brickrouge.from(el.querySelector('.widget-adjust-image'))
			this.adjustImage.observeChange(this.onChange.bind(this))

			el.getFirst('.more').addEvent('click', function(ev) {

				this.element.toggleClass('unfold-thumbnail')
				this.fireEvent('adjust', { target: this.element, widget: this })

			}.bind(this))
		},

		decodeOptions: function(url)
		{
			var capture = url.match(/\/images\/(\d+|[^\/]+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/)
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
				value = el.get('data-nid') || el.get('src')
			}

			if (typeOf(value) == 'string' && value.indexOf('/images/') !== -1)
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

			this.adjustImage.setValue(options.nid)
			this.thumbnailOptions.setValue(options)
		},

		getValue: function()
		{
			var selected = this.adjustImage.getSelected()
			, options = this.thumbnailOptions.getValue()
			, url = null
			, thumbnail = null

			if (selected)
			{
				var nid = selected.get('data-nid')

				try
				{
					thumbnail = new ICanBoogie.Modules.Thumbnailer.Thumbnail('/images/' + nid, options)
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
			var selected = this.adjustImage.getSelected()

			this.fireEvent('change', {

				target: this,
				url: this.getValue(),
				nid: selected ? selected.get('data-nid') : null,
				selected: selected,
				options: this.thumbnailOptions.getValue()

			})
		}
	})

});

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('AdjustThumbnail', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/adjust-thumbnail')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
