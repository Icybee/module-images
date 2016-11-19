define([

	'brickrouge'

], function (Brickrouge) {

	"use strict";

	class UploadFiles extends Brickrouge.File
	{

	}

	Brickrouge.register('UploadFiles', (element, options) => {

		console.log(options)

		return new UploadFiles(element, options)

	})

})
