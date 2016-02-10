
plugin.tx_testextension_testplugin {
	view {
		# cat=plugin.tx_testextension_testplugin/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:test_extension/Resources/Private/Templates/
		# cat=plugin.tx_testextension_testplugin/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:test_extension/Resources/Private/Partials/
		# cat=plugin.tx_testextension_testplugin/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:test_extension/Resources/Private/Layouts/
	}
	persistence {
		# cat=plugin.tx_testextension_testplugin//a; type=string; label=Default storage PID
		storagePid =
	}
}

module.tx_testextension_testmodule1 {
	view {
		# cat=module.tx_testextension_testmodule1/file; type=string; label=Path to template root (BE)
		templateRootPath = EXT:test_extension/Resources/Private/Backend/Templates/
		# cat=module.tx_testextension_testmodule1/file; type=string; label=Path to template partials (BE)
		partialRootPath = EXT:test_extension/Resources/Private/Backend/Partials/
		# cat=module.tx_testextension_testmodule1/file; type=string; label=Path to template layouts (BE)
		layoutRootPath = EXT:test_extension/Resources/Private/Backend/Layouts/
	}
	persistence {
		# cat=module.tx_testextension_testmodule1//a; type=string; label=Default storage PID
		storagePid =
	}
}
