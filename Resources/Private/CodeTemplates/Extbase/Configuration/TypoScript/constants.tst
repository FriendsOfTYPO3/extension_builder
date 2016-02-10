<f:for each="{extension.plugins}" as="plugin">
plugin.{extension.shortExtensionKey}_{plugin.key} {
  view {
    # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template root (FE)
    templateRootPath =
    # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template partials (FE)
    partialRootPath =
    # cat=plugin.{extension.shortExtensionKey}_{plugin.key}/file; type=string; label=Path to template layouts (FE)
    layoutRootPath =
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
    templateRootPath =
    # cat=module.{extension.shortExtensionKey}_{backendModule.key}/file; type=string; label=Path to template partials (BE)
    partialRootPath =
    # cat=module.{extension.shortExtensionKey}_{backendModule.key}/file; type=string; label=Path to template layouts (BE)
    layoutRootPath =
  }
  persistence {
    # cat=module.{extension.shortExtensionKey}_{backendModule.key}//a; type=string; label=Default storage PID
    storagePid =
  }
}
</f:for>
