define('icybee/images/adjust-image', [ 'icybee/nodes/adjust-node' ], function(AdjustNode) {

	return new Class({

		Extends: AdjustNode

	})

})

require([ 'icybee/images/adjust-image' ], function(AdjustImage) {

	Brickrouge.Widget.AdjustImage = AdjustImage

})
;