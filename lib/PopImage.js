define('icybee/images/pop-image', [

	'icybee/nodes/pop-node'

], function(PopNode) {

	const OPTIONS_DEFAULT = {

		thumbnailVersion: '$popimage'

	}

	return class extends PopNode
	{
		constructor(el, options)
		{
			super(el, Object.assign({}, OPTIONS_DEFAULT, options))

			this.img = el.querySelector('img')
			this.loader = this.createLoader(this.img)
		}

		change(ev)
		{
			this.value = parseInt(ev.selected.get('data-nid'))
		}

		formatValue(value)
		{
			const img = this.img

			if (!value) return ''

			this.updateThumbnail(value)

			return img
		}

		createLoader(img) {

			const loader = document.createElement('img')

			loader.onload = function () {

				var parent = img.parentNode

				parent.removeChild(img)

				img.setAttribute('width', loader.naturalWidth)
				img.setAttribute('height', loader.naturalHeight)
				img.src = loader.src

				parent.appendChild(img)

				if (this.popover)
				{
					this.popover.reposition()
				}

			}.bind(this)

			return loader

		}

		updateThumbnail(value)
		{
			this.loader.src = '/images/' + value + '/thumbnails/' + this.options.thumbnailVersion + '?_r=' + Date.now()
		}
	}

})

!function (Brickrouge) {

	let Constructor

	Brickrouge.register('PopImage', (element, options) => {

		if (!Constructor)
		{
			Constructor = require('icybee/images/pop-image')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
