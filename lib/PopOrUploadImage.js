!function (Brickrouge) {

	Brickrouge.Widget.PopOrUploadImage = (function() {

		var UPLOAD_MODE = 'upload-mode'
		, UPLOAD_MODE_CREATE = 'create'
		, UPLOAD_MODE_UPDATE = 'update'

		, constructor = new Class({

			initialize: function(el, options) {

				this.element = el = document.id(el)

				var pop = Brickrouge.from(el.getElement('.widget-pop-image'))
				, upload = Brickrouge.from(el.getElement('.widget-file'))
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

	} ())

	Brickrouge.register('PopOrUploadImage', function (element, options) {

		return new Brickrouge.Widget.PopOrUploadImage(element, options)

	})

} (Brickrouge)
