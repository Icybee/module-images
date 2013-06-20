Brickrouge.Widget.PopOrUploadImage = new Class({

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