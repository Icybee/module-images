define('icybee/images/pop-image', [

	'icybee/nodes/pop-node'

], function(PopNode) {

	return new Class({

		Extends: PopNode,

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options)
		{
			this.parent(el, options)
			this.img = el.querySelector('img')
			this.loader = this.createLoader(this.img)
		},

		change: function(ev)
		{
			this.setValue(ev.selected.get('data-nid'))
		},

		formatValue: function(value)
		{
			var img = this.img

			if (!value) return ''

			this.updateThumbnail(value)

			return img
		},

		createLoader: function (img) {

			var loader = document.createElement('img')

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

		},

		updateThumbnail: function(value)
		{
			this.loader.src = '/images/' + value + '/thumbnails/' + this.options.thumbnailVersion + '?_r=' + Date.now()
		}
	})

})

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('PopImage', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/pop-image')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
