<?php

class Tx_ExtbaseKickstarter_ViewHelpers_Be_ConfigurationViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {
	
	public function render() {
		$doc = $this->getDocInstance();
		$doc->bodyTagAdditions .= 'class="yui-skin-sam"';

		$baseUrl = '../' . t3lib_extMgm::siteRelPath('extbase_kickstarter');

		$pageRenderer = $doc->getPageRenderer();
		$pageRenderer->loadExtJS(false, false);

		$compress = false; // Set to false for debugging purposes

		// SECTION: JAVASCRIPT FILES
		// YUI Basis Files
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/utilities/utilities.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/resize/resize-min.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/layout/layout-min.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/container/container-min.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/json/json-min.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/button/button-min.js', 'text/javascript', $compress);

		// YUI-RPC
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui-rpc.js', 'text/javascript', $compress);

		// InputEx with wirable options
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/inputex.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Field.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/WirableField-beta.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Group.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Visus.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/StringField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/Textarea.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/SelectField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/EmailField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/UrlField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/ListField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/CheckBox.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/InPlaceEdit.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/MenuField.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/TypeField.js', 'text/javascript', $compress);

		// WireIt
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/excanvas.js', 'text/javascript', false, FALSE, '<!--[if IE]>|<![endif]-->');
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WireIt.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/CanvasElement.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Wire.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Terminal.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DD.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DDResize.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Container.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/ImageContainer.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Layer.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/FormContainer-beta.js', 'text/javascript', $compress);
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/LayerMap.js', 'text/javascript', $compress);

		$pageRenderer->addInlineSettingArray('kickstarter', array(
			'baseUrl' => $baseUrl
		));

		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WiringEditor.js', 'text/javascript', $compress);

		// Extbase Modelling definition
		$pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.js', 'text/javascript', $compress);


		// SECTION: CSS Files
		// YUI CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/reset-fonts-grids/reset-fonts-grids.css', 'stylesheet', 'all', '', $compress);
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/assets/skins/sam/skin.css', 'stylesheet', 'all', '', $compress);

		// InputEx CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/css/inputEx.css', 'stylesheet', 'all', '', $compress);

		// WireIt CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireIt.css', 'stylesheet', 'all', '', $compress);
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireItEditor.css', 'stylesheet', 'all', '', $compress);

		// Custom CSS
		$pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.css', 'stylesheet', 'all', '', $compress);

	}
}

?>