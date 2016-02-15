
plugin.tx_testextension_testplugin {
	view {
		templateRootPaths.0 = {$plugin.tx_testextension_testplugin.view.templateRootPath}
		partialRootPaths.0 = {$plugin.tx_testextension_testplugin.view.partialRootPath}
		layoutRootPaths.0 = {$plugin.tx_testextension_testplugin.view.layoutRootPath}
	}
	persistence {
		storagePid = {$plugin.tx_testextension_testplugin.persistence.storagePid}
		#recursive = 1
	}
	features {
		#skipDefaultArguments = 1
	}
	mvc {
		#callDefaultActionIfActionCantBeResolved = 1
	}
}

plugin.tx_testextension._CSS_DEFAULT_STYLE (
	textarea.f3-form-error {
		background-color:#FF9F9F;
		border: 1px #FF0000 solid;
	}

	input.f3-form-error {
		background-color:#FF9F9F;
		border: 1px #FF0000 solid;
	}

	.tx-test-extension table {
		border-collapse:separate;
		border-spacing:10px;
	}

	.tx-test-extension table th {
		font-weight:bold;
	}

	.tx-test-extension table td {
		vertical-align:top;
	}

	.typo3-messages .message-error {
		color:red;
	}

	.typo3-messages .message-ok {
		color:green;
	}

)

 # Module configuration
module.tx_testextension_web_testextensiontestmodule1 {
	persistence {
		storagePid = {$module.tx_testextension_testmodule1.persistence.storagePid}
	}
	view {
		templateRootPaths.0 = {$module.tx_testextension_testmodule1.view.templateRootPath}
		partialRootPaths.0 = {$module.tx_testextension_testmodule1.view.partialRootPath}
		layoutRootPaths.0 = {$module.tx_testextension_testmodule1.view.layoutRootPath}
	}
}
