{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />


/**
 * Repository for {domainObject.className}
 */
class {domainObject.domainRepositoryClassName} extends Tx_Extbase_Persistence_Repository <![CDATA[{]]>

<![CDATA[}]]>
?>