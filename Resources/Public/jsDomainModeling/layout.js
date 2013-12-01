// Configuration of the whole layout. See documentation of YUI's widget.Layout()
extbaseModeling_wiringEditorLanguage.layoutOptions =
{
	units: [
		{
			position: 'left',
			width: 280,
			resize: true,
			body: 'left',
			gutter: '5px',
			collapse: true,
			collapseSize: 20,
			header: TYPO3.settings.extensionBuilder._LOCAL_LANG.extensionProperties,
			scroll: true,
			animate: false
		},
		{
			position: 'center',
			body: 'center',
			gutter: '5px',
			scroll: false
		},
		{
			position: 'bottom',
			height: 27,
			body: 'bottom'
		}
	]
};