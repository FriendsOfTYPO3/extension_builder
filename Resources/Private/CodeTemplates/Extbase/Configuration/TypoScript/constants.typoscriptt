<f:for each="{extension.plugins}" as="plugin">
plugin.{extension.shortExtensionKey}_{plugin.key} {
    view {
        # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:{extension.extensionKey}/Resources/Private/Templates/
        # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:{extension.extensionKey}/Resources/Private/Partials/
        # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:{extension.extensionKey}/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.{extension.shortExtensionKey}_{plugin.key}//a; type=string; label=Default storage PID
        storagePid =
    }
}
</f:for>
<f:for each="{extension.backendModules}" as="backendModule">
module.{extension.shortExtensionKey}_{backendModule.key} {
    view {
        # cat=module.{extension.shortExtensionKey}_{backendModule.key}/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:{extension.extensionKey}/Resources/Private/Backend/Templates/
        # cat=module.{extension.shortExtensionKey}_{backendModule.key}/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:{extension.extensionKey}/Resources/Private/Backend/Partials/
        # cat=module.{extension.shortExtensionKey}_{backendModule.key}/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:{extension.extensionKey}/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.{extension.shortExtensionKey}_{backendModule.key}//a; type=string; label=Default storage PID
        storagePid =
    }
}
</f:for>
