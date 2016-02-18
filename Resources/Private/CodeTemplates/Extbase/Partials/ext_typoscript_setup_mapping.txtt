{namespace k=EBT\ExtensionBuilder\ViewHelpers}
  persistence {
    classes {<f:for each="{extension.domainObjectsThatNeedMappingStatements}" as="domainObject">
      {domainObject.className} {
        mapping {
          <f:if condition="{domainObject.mapToTable}">tableName = {domainObject.databaseTableName}
          recordType = {domainObject.recordType}</f:if>
          <f:if condition="{domainObject.propertiesWithMappingStatements}">columns <![CDATA[{]]><f:for each="{domainObject.propertiesWithMappingStatements}" as="property">
            {property.mappingStatement}</f:for>
          }</f:if>
        }
      }</f:for>
    }
  }
