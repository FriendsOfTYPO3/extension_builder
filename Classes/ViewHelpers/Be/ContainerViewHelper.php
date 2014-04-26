<?php
namespace EBT\ExtensionBuilder\ViewHelpers\Be;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen <mail@ndh-websolutions.de>, ndh websolutions
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This is only needed as a workaround for #58075
 * can be removed if the option includeCsh is added
 * in Core
 *
 * Class ContainerViewHelper
 * @package EBT\ExtensionBuilder\ViewHelpers\Be
 */

class ContainerViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\ContainerViewHelper {
	/**
	 * Render start page with \TYPO3\CMS\Backend\Template\DocumentTemplate and pageTitle
	 *
	 * @param string  $pageTitle title tag of the module. Not required by default, as BE modules are shown in a frame
	 * @param boolean $enableJumpToUrl If TRUE, includes "jumpTpUrl" javascript function required by ActionMenu. Defaults to TRUE, deprecated, as not needed anymore
	 * @param boolean $enableClickMenu If TRUE, loads clickmenu.js required by BE context menus. Defaults to TRUE
	 * @param boolean $loadPrototype specifies whether to load prototype library. Defaults to TRUE
	 * @param boolean $loadScriptaculous specifies whether to load scriptaculous libraries. Defaults to FALSE
	 * @param string  $scriptaculousModule additionales modules for scriptaculous
	 * @param boolean $loadExtJs specifies whether to load ExtJS library. Defaults to FALSE
	 * @param boolean $loadExtJsTheme whether to load ExtJS "grey" theme. Defaults to FALSE
	 * @param string  $extJsAdapter load alternative adapter (ext-base is default adapter)
	 * @param boolean $enableExtJsDebug if TRUE, debug version of ExtJS is loaded. Use this for development only
	 * @param string $addCssFile Custom CSS file to be loaded (deprecated, use $includeCssFiles)
	 * @param string $addJsFile Custom JavaScript file to be loaded (deprecated, use $includeJsFiles)
	 * @param boolean $loadJQuery whether to load jQuery library. Defaults to FALSE
	 * @param array $includeCssFiles List of custom CSS file to be loaded
	 * @param array $includeJsFiles List of custom JavaScript file to be loaded
	 * @param array $addJsInlineLabels Custom labels to add to JavaScript inline labels
	 * @param boolean $includeCsh flag for including CSH
	 * @return string
	 * @see \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @see \TYPO3\CMS\Core\Page\PageRenderer
	 */
	public function render($pageTitle = '', $enableJumpToUrl = TRUE, $enableClickMenu = TRUE, $loadPrototype = TRUE, $loadScriptaculous = FALSE, $scriptaculousModule = '', $loadExtJs = FALSE, $loadExtJsTheme = TRUE, $extJsAdapter = '', $enableExtJsDebug = FALSE, $addCssFile = NULL, $addJsFile = NULL, $loadJQuery = FALSE, $includeCssFiles = NULL, $includeJsFiles = NULL, $addJsInlineLabels = NULL, $includeCsh = TRUE) {
		$doc = $this->getDocInstance();
		$pageRenderer = $doc->getPageRenderer();
		$doc->JScode .= $doc->wrapScriptTags($doc->redirectUrls());

		// Load various standard libraries
		if ($enableClickMenu) {
			$doc->loadJavascriptLib('sysext/backend/Resources/Public/JavaScript/clickmenu.js');
		}
		if ($loadPrototype) {
			$pageRenderer->loadPrototype();
		}
		if ($loadScriptaculous) {
			$pageRenderer->loadScriptaculous($scriptaculousModule);
		}
		if ($loadExtJs) {
			$pageRenderer->loadExtJS(TRUE, $loadExtJsTheme, $extJsAdapter);
			if ($enableExtJsDebug) {
				$pageRenderer->enableExtJsDebug();
			}
		}
		if ($loadJQuery) {
			$pageRenderer->loadJquery(NULL, NULL, $pageRenderer::JQUERY_NAMESPACE_DEFAULT_NOCONFLICT);
		}
		// This way of adding a single CSS or JS file is deprecated, the array below should be used instead
		if ($addCssFile !== NULL) {
			GeneralUtility::deprecationLog('Usage of addCssFile attribute is deprecated since TYPO3 CMS 6.2. It will be removed in TYPO3 CMS 7.0. Use includeCssFiles instead.');
			$pageRenderer->addCssFile($addCssFile);
		}
		if ($addJsFile !== NULL) {
			GeneralUtility::deprecationLog('Usage of addJsFile attribute is deprecated since TYPO3 CMS 6.2. It will be removed in TYPO3 CMS 7.0. Use includeJsFiles instead.');
			$pageRenderer->addJsFile($addJsFile);
		}
		// Include custom CSS and JS files
		if (is_array($includeCssFiles) && count($includeCssFiles) > 0) {
			foreach ($includeCssFiles as $addCssFile) {
				$pageRenderer->addCssFile($addCssFile);
			}
		}
		if (is_array($includeJsFiles) && count($includeJsFiles) > 0) {
			foreach ($includeJsFiles as $addJsFile) {
				$pageRenderer->addJsFile($addJsFile);
			}
		}
		// Add inline language labels
		if (is_array($addJsInlineLabels) && count($addJsInlineLabels) > 0) {
			$extensionKey = $this->controllerContext->getRequest()->getControllerExtensionKey();
			foreach ($addJsInlineLabels as $key) {
				$label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, $extensionKey);
				$pageRenderer->addInlineLanguageLabel($key, $label);
			}
		}
		// Render the content and return it
		$output = $this->renderChildren();
		$output = $doc->startPage($pageTitle, $includeCsh) . $output;
		$output .= $doc->endPage();
		return $output;
	}

}