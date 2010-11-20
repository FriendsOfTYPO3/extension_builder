// Configuration of the whole layout. See documentation of YUI's widget.Layout()
extbaseModeling_wiringEditorLanguage.layoutOptions =
{
	units:
	[
		{
			position: 'top',
			height: 50,
			body: 'top'
		},
		{
			position: 'left',
			width: 500,
			resize: true,
			body: 'left',
			gutter: '5px',
			collapse: true,
			collapseSize: 25,
			header: 'Extension Configuration',
			scroll: true,
			animate: false
		},
		{
			position: 'center',
			header: 'Domain Modeling',
			body: 'center',
			gutter: '5px',
			collapse: true,
			collapseSize: 25
		},
		{
			position: 'right',
			width: 500,
			resize: true,
			body: 'right',
			gutter: '5px',
			collapse: true,
			collapseSize: 25,
			header: 'Code Generator',
			scroll: true,
			animate: false
		},
		{
			position: 'bottom',
			height: 40,
			body: 'bottom'
		}
	]
};