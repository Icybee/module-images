Brickrouge.Widget.PopImage = (function() {

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

}) ();