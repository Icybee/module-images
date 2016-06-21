define('icybee/images/adjust-thumbnail', [

	'brickrouge',
	'icybee/thumbnailer/thumbnail',
	'icybee/thumbnailer/version'

], (Brickrouge, Thumbnail, Version) => {

	return new Class({

		Implements: [ Events ],

		initialize: function(el, options)
		{
			this.element = el
			this.control = el.querySelector('input[type="hidden"]')
			this.thumbnailOptions = Brickrouge.from(el.querySelector('.widget-adjust-thumbnail-options'))
			this.thumbnailOptions.addEvent('change', this.onChange.bind(this))

			/**
			 * @type {Icybee.AdjustImage}
			 */
			this.adjustImage = Brickrouge.from(el.querySelector('.widget-adjust-image'))
			this.adjustImage.observeChange(this.onChange.bind(this))

			el.getFirst('.more').addEvent('click', function(ev) {

				this.element.toggleClass('unfold-thumbnail')
				this.fireEvent('adjust', { target: this.element, widget: this })

			}.bind(this))
		},

		/**
		 * @param {string} url
		 *
		 * @returns {object}
		 */
		decodeOptions: function(url)
		{
			const capture = url.match(/\/images\/(\d+|[^\/]+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/)
			const nid = capture[1]
			const width = capture[3] || null
			const height = capture[4] || null
			const method = capture[6]
			const qs = url.indexOf('?')

			let options = {}

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
			let options = { nid: null }
			let el = null

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

			this.adjustImage.value = options.nid
			this.thumbnailOptions.setValue(options)
		},

		getValue: function()
		{
			const selected = this.adjustImage.selected
			const options = this.thumbnailOptions.getValue()
			let url = null
			let thumbnail = null

			if (selected)
			{
				let nid = selected.get('data-nid')

				try
				{
					thumbnail = new Thumbnail('/images/' + nid, options)
					url = thumbnail.toString()
				}
				catch (e)
				{
					if (console) console.log(e)
				}
			}

			return url
		},

		/**
		 * @param {Icybee.AdjustNode.ChangeEvent} ev
		 */
		onChange: function(ev)
		{
			this.fireEvent('change', {

				target: this,
				url: this.getValue(),
				nid: ev.value,
				selected: ev.selected,
				options: this.thumbnailOptions.getValue()

			})
		}
	})

});

!function (Brickrouge) {

	let Constructor

	Brickrouge.register('AdjustThumbnail', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/adjust-thumbnail')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
