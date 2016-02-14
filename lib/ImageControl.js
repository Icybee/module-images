/**
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

			this.setValue(response.rc.key)
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

		setValue: function(value) {

			this.nidElement.value = value
			this.fireEvent('change', value)

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

	Brickrouge.register('ImageControl', function (element, options) {

		return new ImageControl(element, options)

	})

});
