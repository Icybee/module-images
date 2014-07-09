define('icybee/images/save-operation', [], function() {

	return new Class({

		options: {/*
			onRequest: function(){},
			onSuccess: function(){},
			onFailure: function(){},
			onComplete: function(){},
			onProgress: function(){},*/

			constructor: 'files',
			url: ''

		},

		Implements: [ Options, Events ],

		initialize: function(options) {

			this.setOptions(options)

		},

		process: function(properties) {

			var xhr = new XMLHttpRequest()
			, fd = this.createFormData(properties)

			xhr.onreadystatechange = this.onReadyStateChange.bind(this)
			xhr.upload.onprogress = this.onProgress.bind(this)
			xhr.upload.onload = this.onProgress.bind(this)

			xhr.open("POST", this.options.url)

			xhr.setRequestHeader('Accept', 'application/json')
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
			xhr.setRequestHeader('X-Request', 'JSON')

			this.request(xhr)

			xhr.send(fd)

		},

		createFormData: function(properties) {

			var data = new FormData()

			data.append(ICanBoogie.Operation.DESTINATION, this.options.constructor)
			data.append(ICanBoogie.Operation.NAME, 'save')

			Object.each(properties, function(value, key) {

				data.append(key, value)

			})

			return data
		},

		/**
		 * An event callback that invokes `status()`, `failure()` and `complete()` according to the
		 * state of the request.
		 *
		 * If an error occurs during `complete()`, or if the response cannot be decoded,
		 * `failure()` is invoked.
		 *
		 * @param readystatechange ev
		 */
		onReadyStateChange: function(ev) {

			var xhr = ev.target

			if (xhr.readyState != XMLHttpRequest.DONE)
			{
				return
			}

			if (xhr.status == 200)
			{
				try
				{
					this.success(JSON.decode(xhr.responseText), xhr)
				}
				catch (e)
				{
					if (console) console.error(e)

					this.failure(xhr)
				}
			}
			else if (xhr.status >= 400)
			{
				this.failure(xhr)
			}

			this.complete(xhr)

		},

		onProgress: function(ev) {

			this.fireEvent('progress', ev)

		},

		/**
		 * Fires the `request` event.
		 */
		request: function(xhr) {

			this.fireEvent('request', xhr)

		},

		/**
		 * Updates the value of the key element and fires the `sucess` event.
		 *
		 * @param response
		 * @param xhr
		 */
		success: function(response, xhr) {

			this.fireEvent('success', response, xhr)

		},

		/**
		 * Fires the `failure` event.
		 *
		 * @param xhr
		 */
		failure: function(xhr) {

			this.fireEvent('failure', xhr)

		},

		/**
		 * Fires the `complete` event.
		 *
		 * @param xhr
		 */
		complete: function(xhr) {

			this.fireEvent('complete', xhr)

		}

	})

});/**
 * ImageControl widget.
 */
define('icybee/images/image-control', [ 'icybee/images/save-operation' ], function(SaveOperation) {

	return new Class({

		options: {

			maxFileSize: 0,
			maxFileSizeAlert: "The selected file is too big.",
			acceptedExtensions: null,
			acceptedExtensionsAlert: "Wrong file type."

		},

		Implements: [ Options, Events ],

		initialize: function(el, options) {

			this.element = el = document.id(el)
			this.fileElement = el.getElement('[type="file"]')
			this.nidElement = el.getElement('[type="hidden"]')
			this.progressPositionElement = el.getElement('.progress-position')
			this.alertContentElement = el.getElement('.alert .content')
			this.defaultAlertMessage = this.alertContentElement.innerHTML

			this.setOptions(options)

			if (typeOf(this.options.acceptedExtensions) == 'string')
			{
				this.options.acceptedExtensions = this.options.acceptedExtensions.split(' ')
			}

			this.fileElement.addEvent('change', function(ev) {

				this.start()

			}.bind(this))

			el.addEventListener('dragover', this.onDragover.bind(this), false)
			el.addEventListener('dragleave', this.onDragleave.bind(this), false)
			el.addEventListener('drop', this.onDrop.bind(this), false)
		},

		onDragover: function(ev) {

			ev.preventDefault()
			this.element.addClass('dnd-hover')

		},

		onDragleave: function(ev) {

			ev.preventDefault()
			this.element.removeClass('dnd-hover')

		},

		onDrop: function(ev) {

			ev.stopPropagation()
			ev.preventDefault()

			this.element.removeClass('dnd-hover')

			var files = ev.target.files || ev.dataTransfer.files

			this.upload(files[0])
		},

		/**
		 * Updates the position of the progress element.
		 *
		 * @param float position The position expressed as a float from 0 to 1.
		 */
		setPosition: function(position) {

			this.progressPositionElement.setStyle('width', position * 100 + '%')
			this.element.getElement('.progress-label').innerHTML = Math.round(100 * position) + '%'

		},

		/**
		 * Displays an alert.
		 */
		alert: function(message) {

			this.alertContentElement.innerHTML = message
			this.element.addClass('has-error')

		},

		/**
		 * Validates the file to upload against a set of rules.
		 */
		validate: function(file) {

			var acceptedExtensions = this.options.acceptedExtensions
			, maxFileSize = this.options.maxFileSize

			if (acceptedExtensions)
			{
				acceptedExtensions = (acceptedExtensions.join('|')).replace(/\./g, '')
				acceptedExtensions = new RegExp('\.(' + acceptedExtensions + ')$')

				if (!acceptedExtensions.test(file.name))
				{
					this.alert(this.options.acceptedExtensionsAlert)

					return false
				}
			}

			if (file.size > maxFileSize)
			{
				this.alert(this.options.maxFileSizeAlert)

				return false
			}

			return true

		},

		/**
		 * Starts the upload of the file specified by the file element.
		 */
		start: function() {

			var files = this.fileElement.files

			this.upload(files[0])
		},

		/**
		 * Upload a file.
		 *
		 * @param File file
		 */
		upload: function(file) {

			if (!this.validate(file)) return

			var operation = new SaveOperation({

				constructor: 'images',

				onRequest: this.request.bind(this),
				onSuccess: this.success.bind(this),
				onFailure: this.failure.bind(this),
				onComplete: this.complete.bind(this),
				onProgress: this.progress.bind(this)

			})

			, nid = this.nidElement.get('value')
			, properties = { path: file, is_online: true }

			if (nid)
			{
				properties[ICanBoogie.Operation.KEY] = nid
			}

			operation.process(properties)
		},

		/**
		 * Resets the progress position and alert, and fires the `request` event.
		 *
		 * The method is invoked right before the request is sent.
		 */
		request: function(xhr) {

			this.setPosition(0)
			this.element.removeClass('has-error')
			this.element.addClass('uploading')

			this.fireEvent('request', arguments)
		},

		/**
		 * Updates the value of the key element and fires the `success` event.
		 *
		 * The method is invoked after the request completed successfully
		 *
		 * @param response
		 * @param xhr
		 */
		success: function(response, xhr) {

			this.nidElement.value = response.rc.key

			this.fireEvent('success', arguments)

		},

		/**
		 * Fires the `failure` event.
		 *
		 * The method is invoked when the request fails.
		 *
		 * @param xhr
		 */
		failure: function(xhr) {

			var message = this.defaultAlertMessage

			try
			{
				response = JSON.decode(xhr.responseText)

				if (response.errors.path)
				{
					message = response.errors.path
				}
				else if (response.exception)
				{
					message = response.errors.path
				}
			}
			catch(e) {}

			this.alert(message)

			this.fireEvent('failure', arguments)

		},

		/**
		 * Fires the `complete` event.
		 *
		 * The method is invoked after the request completed, successfully or not.
		 *
		 * @param xhr
		 */
		complete: function(xhr) {

			this.element.removeClass('uploading')

			this.fireEvent('complete', arguments)
			this.fireEvent('change', this.getValue())

		},

		/**
		 * Updates the progress element and fires the `progress` event.
		 *
		 * @param progress
		 */
		progress: function(progress) {

			if (progress.lengthComputable)
			{
				var position = progress.loaded / progress.total

				this.setPosition(position)
			}

			this.fireEvent('progress', arguments)

		},

		getValue: function() {

			return this.nidElement.value

		}

	})

})

/**
 * Register Brickrouge widget "ImageControl".
 */
define([ 'icybee/images/image-control' ], function(ImageControl) {

	Brickrouge.Widget.ImageControl = ImageControl

})
;define('icybee/images/image-control-with-preview', [ 'icybee/images/image-control' ], function(ImageControl) {

	return new Class({

		options: {

			thumbnailVersion: '$popimage'

		},

		initialize: function(el, options) {

			var control

			this.element = el = document.id(el)
			this.imgElement = el.getElement('img')
			this.control = control = el.getElement(Brickrouge.WIDGET_SELECTOR).get('widget')

			this.control.addEvent('change', function(value) {

				this.setValue(value)

			}.bind(this))

			el.addEventListener('dragover', function(ev) { control.onDragover(ev) }, false)
			el.addEventListener('dragleave', function(ev) { control.onDragleave(ev) }, false)
			el.addEventListener('drop', function(ev) { control.onDrop(ev) }, false)
		},

		setValue: function(value) {

			this.imgElement.src = "/api/images/" + value + "/thumbnails/" + this.options.thumbnailVersion + "?" + (new Date().getTime()).toString(16)

		},

		getValue: function(value) {

			return this.control.getValue()

		}

	})

})

/**
 * Register Brickrouge widget "ImageControlWithPreview"
 */
define([ 'icybee/images/image-control-with-preview' ], function(ImageControlWithPreview) {

	Brickrouge.Widget.ImageControlWithPreview = ImageControlWithPreview

})
;define('icybee/images/adjust-image', [ 'icybee/nodes/adjust-node' ], function(AdjustNode) {

	return new Class({

		Extends: AdjustNode

	})

})

define([ 'icybee/images/adjust-image' ], function(AdjustImage) {

	Brickrouge.Widget.AdjustImage = AdjustImage

})
;Brickrouge.Widget.AdjustThumbnail = new Class({

	Implements: [ Events ],

	initialize: function(el, options)
	{
		this.element = el = document.id(el)
		this.control = el.getElement('input[type="hidden"]')
		this.thumbnailOptions = el.getElement('.widget-adjust-thumbnail-options').get('widget')
		this.image = el.getElement('.widget-adjust-image').get('widget')

		this.thumbnailOptions.addEvent('change', this.onChange.bind(this))
		this.image.addEvent('change', this.onChange.bind(this))

		el.getFirst('.more').addEvent('click', function(ev) {

			this.element.toggleClass('unfold-thumbnail')
			this.fireEvent('adjust', { target: this.element, widget: this })

		}.bind(this))
	},

	decodeOptions: function(url)
	{
		var capture = url.match(/\/api\/images\/(\d+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/)
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

		if (typeOf(value) == 'string' && value.indexOf('/api/') !== -1)
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

		this.image.setValue(options.nid)
		this.thumbnailOptions.setValue(options)
	},

	getValue: function()
	{
		var selected = this.image.getSelected()
		, options = this.thumbnailOptions.getValue()
		, url = null
		, thumbnail = null

		if (selected)
		{
			url = selected.get('data-path')

			try
			{
				thumbnail = new ICanBoogie.Modules.Thumbnailer.Thumbnail(url, options)
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
		var selected = this.image.getSelected()

		this.fireEvent('change', {

			target: this,
			url: this.getValue(),
			nid: selected ? selected.get('data-nid') : null,
			selected: selected,
			options: this.thumbnailOptions.getValue()

		})
	}
});define('icybee/images/pop-image', [ 'icybee/nodes/pop-node' ], function(PopNode) {

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

			img.src = Request.API.encode('images/' + value + '/thumbnails/' + this.options.thumbnailVersion + '?_r=' + Date.now())

			return img
		}
	})

})

define([ 'icybee/images/pop-image' ], function(PopImage) {

	Brickrouge.Widget.PopImage = PopImage

})
;Brickrouge.Widget.PopOrUploadImage = (function() {

	var UPLOAD_MODE = 'upload-mode'
	, UPLOAD_MODE_CREATE = 'create'
	, UPLOAD_MODE_UPDATE = 'update'

	, constructor = new Class({

		initialize: function(el, options) {

			this.element = el = document.id(el)

			var pop = el.getElement('.widget-pop-image').get('widget')
			, upload = el.getElement('.widget-file').get('widget')
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

} ());