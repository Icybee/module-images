define('icybee/images/pop-image', [ 'icybee/nodes/pop-node' ], function(PopNode) {

	return new Class({

		Extends: PopNode,

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

			img.src = '/images/' + value + '/thumbnails/' + this.options.thumbnailVersion + '?_r=' + Date.now()

			return img
		}
	})

})

define([ 'icybee/images/pop-image' ], function(PopImage) {

	Brickrouge.Widget.PopImage = PopImage

});
