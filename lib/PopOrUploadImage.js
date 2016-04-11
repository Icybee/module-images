define('icybee/images/pop-or-upload-image', [

	'brickrouge'

], function (Brickrouge) {

	const UPLOAD_MODE = 'upload-mode'
	const UPLOAD_MODE_CREATE = 'create'
	const UPLOAD_MODE_UPDATE = 'update'

	class PopOrUploadImage
	{
		/**
		 * @param {Element} el
		 * @param {object} options
		 */
		constructor(el, options)
		{
			this.element = el
			this.options = options

			const pop = Brickrouge.from(el.querySelector('.widget-pop-image'))
			const upload = Brickrouge.from(el.querySelector('.widget-file'))
			let nid = null

			if (upload.options.uploadMode == UPLOAD_MODE_UPDATE)
			{
				nid = pop.getValue()
			}

			upload.observe(Brickrouge.File.EVENT_PREPARE, ev => {

				const data = ev.data

				data.append('title', ev.file.name)
				data.append('is_online', true)

				if (nid)
				{
					data.append(ICanBoogie.Operation.KEY, nid)
				}

			})

			upload.observe(Brickrouge.File.EVENT_SUCCESS, ev => {

				pop.setValue(ev.response.rc.key)

			})
		}
	}

	Object.defineProperties(PopOrUploadImage, {

		UPLOAD_MODE:        { value: UPLOAD_MODE },
		UPLOAD_MODE_CREATE: { value: UPLOAD_MODE_CREATE },
		UPLOAD_MODE_UPDATE: { value: UPLOAD_MODE_UPDATE }

	})

	return PopOrUploadImage

})

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('PopOrUploadImage', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/pop-or-upload-image')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
