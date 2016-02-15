<f:for each="{extension.plugins}" as="plugin">
plugin.{extension.shortExtensionKey}_{plugin.key} {
  view {
    templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Templates/
    templateRootPaths.1 = <![CDATA[{$]]>plugin.{extension.shortExtensionKey}_{plugin.key}.view.templateRootPath<![CDATA[}]]>
    partialRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Partials/
    partialRootPaths.1 = <![CDATA[{$]]>plugin.{extension.shortExtensionKey}_{plugin.key}.view.partialRootPath<![CDATA[}]]>
    layoutRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Layouts/
    layoutRootPaths.1 = <![CDATA[{$]]>plugin.{extension.shortExtensionKey}_{plugin.key}.view.layoutRootPath<![CDATA[}]]>
  }
  persistence {
    storagePid = <![CDATA[{$]]>plugin.{extension.shortExtensionKey}_{plugin.key}.persistence.storagePid<![CDATA[}]]>
    #recursive = 1
  }
  features {
    #skipDefaultArguments = 1
  }
  mvc {
    #callDefaultActionIfActionCantBeResolved = 1
  }
}
</f:for>

<f:if condition="{extension.plugins}">
plugin.{extension.shortExtensionKey}._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    .{extension.cssClassName} table {
        border-collapse:separate;
        border-spacing:10px;
    }

    .{extension.cssClassName} table th {
        font-weight:bold;
    }

    .{extension.cssClassName} table td {
        vertical-align:top;
    }

    .typo3-messages .message-error {
        color:red;
    }

    .typo3-messages .message-ok {
        color:green;
    }
)
</f:if>

<f:for each="{extension.backendModules}" as="backendModule">
# Module configuration
module.{extension.shortExtensionKey}_{backendModule.mainModule}_{extension.unprefixedShortExtensionKey}{backendModule.key} {
  persistence {
    storagePid = <![CDATA[{$]]>module.{extension.shortExtensionKey}_{backendModule.key}.persistence.storagePid<![CDATA[}]]>
  }
  view {
    templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Backend/Templates/
    templateRootPaths.1 = <![CDATA[{$]]>module.{extension.shortExtensionKey}_{backendModule.key}.view.templateRootPath<![CDATA[}]]>
    partialRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Backend/Partials/
    partialRootPaths.1 = <![CDATA[{$]]>module.{extension.shortExtensionKey}_{backendModule.key}.view.partialRootPath<![CDATA[}]]>
    layoutRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Backend/Layouts/
    layoutRootPaths.1 = <![CDATA[{$]]>module.{extension.shortExtensionKey}_{backendModule.key}.view.layoutRootPath<![CDATA[}]]>
  }
}
</f:for>
