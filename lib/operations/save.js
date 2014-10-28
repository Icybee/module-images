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

});
