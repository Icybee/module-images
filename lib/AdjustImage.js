define('icybee/images/adjust-image', [

	'icybee/nodes/adjust-node'

], function(AdjustNode) {

	return AdjustNode

});

!function (Brickrouge) {

	var Constructor

	Brickrouge.register('AdjustImage', function (element, options) {

		if (!Constructor)
		{
			Constructor = require('icybee/images/adjust-image')
		}

		return new Constructor(element, options)

	})

} (Brickrouge)
