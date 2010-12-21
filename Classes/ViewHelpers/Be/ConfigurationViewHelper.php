<?php

class Tx_ExtbaseKickstarter_ViewHelpers_Be_ConfigurationViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {
	
	public function render() {
		
		$doc = $this->getDocInstance();
		$doc->bodyTagAdditions .= 'class="yui-skin-sam"';

		$baseUrl = '../' . t3lib_extMgm::siteRelPath('extbase_kickstarter');

		$pageRenderer = $doc->getPageRenderer();
		$pageRenderer->disableCompressJavascript();
		$pageRenderer->loadExtJS(false, false);

		// SECTION: JAVASCRIPT FILES
		// YUI Basis Files
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/utilities/utilities.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/resize/resize-min.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/layout/layout-min.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/container/container-min.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/json/json-min.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/button/button-min.js');

		// YUI-RPC
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui-rpc.js');

		// InputEx with wirable options
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/inputex.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Field.js');
		
		// extended fields for enabling unique ids
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extended/ListField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extended/Group.js');
		
		
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/WirableField-beta.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Visus.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/StringField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/Textarea.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/SelectField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/EmailField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/UrlField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/CheckBox.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/InPlaceEdit.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/MenuField.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/TypeField.js');

		// WireIt
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/excanvas.js', 'text/javascript', TRUE, FALSE, '<!--[if IE]>|<![endif]-->');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WireIt.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/CanvasElement.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Wire.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Terminal.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DD.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DDResize.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Container.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/ImageContainer.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Layer.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/FormContainer-beta.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/LayerMap.js');
		
		$pageRenderer->addInlineSettingArray('kickstarter', array(
			'baseUrl' => $baseUrl
		));
		
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WiringEditor.js');
		
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/roundtrip.js');
		
		
		// Extbase Modelling definition
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/layout.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extensionProperties.js');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/modules/modelObject.js');
		
		// collapsible forms in relations
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/modules/extendedModelObject.js');

		// SECTION: CSS Files
		// YUI CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/reset-fonts-grids/reset-fonts-grids.css');
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/assets/skins/sam/skin.css');

		// InputEx CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/css/inputEx.css');

		// WireIt CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireIt.css');
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireItEditor.css');

		// Custom CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.css');

	}
}

?>