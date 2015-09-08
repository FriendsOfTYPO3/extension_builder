array(
	'type' => 'text',
	'cols' => 40,
	'rows' => 15,
	'eval' => 'trim<f:if condition="{property.required}">,required</f:if>',
	'wizards' => array(
		'RTE' => array(
			'icon' => 'wizard_rte2.gif',
			'notNewRecords'=> 1,
			'RTEonly' => 1,
			'module' => array(
				'name' => 'wizard_rich_text_editor',
				'urlParameters' => array(
					'mode' => 'wizard',
					'act' => 'wizard_rte.php'
				)
			),
			'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
			'type' => 'script'
		)
	)
),