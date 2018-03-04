
plugin.tx_testextension_testplugin {
    view {
        templateRootPaths.0 = EXT:test_extension/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_testextension_testplugin.view.templateRootPath}
        partialRootPaths.0 = EXT:test_extension/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_testextension_testplugin.view.partialRootPath}
        layoutRootPaths.0 = EXT:test_extension/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_testextension_testplugin.view.layoutRootPath}
    }
    persistence {
        # Be aware that a manual storage assignment via "Record Storage Page" in the
        # backend will not have any effect once a storagePid is set via TypoScript.
        # See https://forge.typo3.org/issues/58857
        storagePid = {$plugin.tx_testextension_testplugin.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
}

# these classes are only used in auto-generated templates
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
        templateRootPaths.0 = EXT:test_extension/Resources/Private/Backend/Templates/
        templateRootPaths.1 = {$module.tx_testextension_testmodule1.view.templateRootPath}
        partialRootPaths.0 = EXT:test_extension/Resources/Private/Backend/Partials/
        partialRootPaths.1 = {$module.tx_testextension_testmodule1.view.partialRootPath}
        layoutRootPaths.0 = EXT:test_extension/Resources/Private/Backend/Layouts/
        layoutRootPaths.1 = {$module.tx_testextension_testmodule1.view.layoutRootPath}
    }
}
