define('icybee/images/adjust-image', [

	'icybee/nodes/adjust-node'

], AdjustNode => {

	return AdjustNode

})

!function (Brickrouge) {

	let Constructor

	Brickrouge.register('AdjustImage', (element, options) => {

		if (!Constructor)
		{
			Constructor = require('icybee/images/adjust-image')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
