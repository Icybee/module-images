window.addEvent('icybee.manageblock.ready', function(manager) {

	function scan()
	{
		Slimbox.scanPage()
	}

	manager.addEvent('update', scan)

	scan()

})