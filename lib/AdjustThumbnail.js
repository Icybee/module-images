define('icybee/images/adjust-thumbnail', [

	'brickrouge',
	'icybee/adjust',
	'icybee/thumbnailer/thumbnail',
	'icybee/thumbnailer/version'

],

/**
 *
 * @param {Brickrouge} Brickrouge
 * @param {Icybee.Adjust} Adjust
 * @param {Icybee.Thumbnailer.Thumbnail} Thumbnail
 * @param {Icybee.Thumbnailer.Version} Version
 *
 * @returns {Icybee.Images.AdjustThumbnail}
 */
(Brickrouge, Adjust, Thumbnail, Version) => {

	/**
	 * @type {Icybee.Images.AdjustThumbnail.ChangeEvent|Function}
	 */
	const ChangeEvent = Brickrouge.Subject.createEvent(function (target, value, nid, options) {

		this.target = target
		this.value = value
		this.nid = nid
		this.options = options

	})

	const LayoutEvent = Adjust.LayoutEvent

	return class extends Adjust {

		constructor(element, options)
		{
			super(element, options)

			this.control = element.querySelector('input[type="hidden"]')
			this.thumbnailOptions = Brickrouge.from(element.querySelector('.widget-adjust-thumbnail-options'))
			this.thumbnailOptions.addEvent('change', this.onChange.bind(this))

			/**
			 * @type {Icybee.Images.AdjustImage}
			 */
			this.adjustImage = Brickrouge.from(element.querySelector('.widget-adjust-image'))
			this.adjustImage.observeChange(this.onChange.bind(this))

			element.getFirst('.more').addEvent('click', ev => {

				this.element.classList.toggle('unfold-thumbnail')
				this.notify(new LayoutEvent(this))

			})
		}

		/**
		 * @param {string} url
		 *
		 * @returns {object}
		 */
		decodeOptions(url)
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
		}

		/**
		 * @return {string|null}
		 */
		get value()
		{
			const selected = this.adjustImage.selected
			const options = this.thumbnailOptions.getValue()

			if (!selected)
			{
				return null
			}

			let nid = selected.getAttribute('data-nid')

			try
			{
				let thumbnail = new Thumbnail('/images/' + nid, options)

				return thumbnail.toString()
			}
			catch (e)
			{
				console.error(e)
			}

			return null
		}

		/**
		 * @param {HTMLElement|string} value
		 */
		set value(value)
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
			this.thumbnailOptions.value = options
		}

		/**
		 * @param {Icybee.Nodes.AdjustNode.ChangeEvent} ev
		 */
		onChange(ev)
		{
			console.log('change:', ev)

			this.notify(new ChangeEvent(this, this.value, ev.value, this.thumbnailOptions.getValue()))
		}

		/**
		 * @param {Function} callback
		 */
		observeChange(callback)
		{
			console.log('notifyChange:', callback)

			this.observe(ChangeEvent, callback)
		}
	}

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
