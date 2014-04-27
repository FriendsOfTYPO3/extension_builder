<?php
namespace EBT\ExtensionBuilder\ViewHelpers\Be;

class ConfigurationViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper {
	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 */
	private $pageRenderer = NULL;

	public function render() {

		$this->pageRenderer = $this->getDocInstance()->getPageRenderer();

		$baseUrl = '../' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('extension_builder');
		$this->pageRenderer->disableCompressJavascript();

		// SECTION: JAVASCRIPT FILES
		// YUI Basis Files
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/utilities/utilities.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/resize/resize-min.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/layout/layout-min.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/container/container-min.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/json/json-min.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/button/button-min.js');

		// YUI-RPC
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui-rpc.js');

		// InputEx with wirable options
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/inputex.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Field.js');

		// extended fields for enabling unique ids
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extended/ListField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extended/Group.js');

		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/WirableField-beta.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Visus.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/StringField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/Textarea.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/SelectField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/EmailField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/UrlField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/CheckBox.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/InPlaceEdit.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/MenuField.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/TypeField.js');

		// WireIt
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/excanvas.js', 'text/javascript', TRUE, FALSE, '<!--[if IE]>|<![endif]-->');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WireIt.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/CanvasElement.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Wire.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Terminal.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DD.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/DDResize.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Container.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/ImageContainer.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/Layer.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/util/inputex/FormContainer-beta.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/LayerMap.js');

		$this->pageRenderer->addInlineSettingArray(
			'extensionBuilder',
			array('baseUrl' => $baseUrl)
		);
		// This call is only done for backwards compatibility for TYPO3 versions below 6.2
		// It can be removed once compatibility for these versions is dropped as since 6.2 this is populated automatically
		$this->pageRenderer->addInlineSettingArray(
			'ajaxUrls',
			array('ExtensionBuilder::wiringEditorSmdEndpoint' => 'ajax.php?ajaxID=ExtensionBuilder%3A%3AwiringEditorSmdEndpoint')
		);
		$this->setLocallangSettings();

		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/js/WiringEditor.js');

		// Extbase Modelling definition
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/layout.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/extensionProperties.js');
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/modules/modelObject.js');

		// collapsible forms in relations
		$this->pageRenderer->addJsFile($baseUrl . 'Resources/Public/jsDomainModeling/modules/extendedModelObject.js');

		// SECTION: CSS Files
		// YUI CSS
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/reset-fonts-grids/reset-fonts-grids.css');
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/yui/assets/skins/sam/skin.css');

		// InputEx CSS
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/lib/inputex/css/inputEx.css');

		// WireIt CSS
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireIt.css');
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/wireit/css/WireItEditor.css');

		// Custom CSS
		$this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/jsDomainModeling/extbaseModeling.css');

	}

	/**
	 * This method loads the locallang.xml file (default language), and
	 * adds all keys found in it to the TYPO3.settings.extension_builder._LOCAL_LANG object
	 * translated into the current language
	 *
	 * Dots in a key are replaced by a _
	 *
	 * Example:
	 *		error.name becomes TYPO3.settings.extension_builder._LOCAL_LANG.error_name
	 *
	 * @return void
	 */
	private function setLocallangSettings() {
		$LL = \TYPO3\CMS\Core\Utility\GeneralUtility::readLLfile('EXT:extension_builder/Resources/Private/Language/locallang.xml', 'default');
		if (!empty($LL['default']) && is_array($LL['default'])) {
			foreach ($LL['default'] as $key => $value) {
				$this->pageRenderer->addInlineSetting(
					'extensionBuilder._LOCAL_LANG',
					str_replace('.', '_', $key),
					\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'extension_builder')
				);
			}
		}
	}
}
