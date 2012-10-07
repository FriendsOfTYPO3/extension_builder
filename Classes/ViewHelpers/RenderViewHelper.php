<?php
/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 *
 * @version $Id: RenderViewHelper.php 2813 2009-07-16 14:02:34Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class Tx_ExtensionBuilder_ViewHelpers_RenderViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @var Tx_Fluid_Core_Parser_TemplateParser
	 */
	protected $templateParser;

	/**
	 *
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_ExtensionBuilder_Domain_Model_Extension
	 */
	protected $extension;

	/**
	 * @param TYPO3\CMS\Fluid\Core\Parser\TemplateParser $templateParser
	 * @return void
	 */
	public function injectTemplateParser(TYPO3\CMS\Fluid\Core\Parser\TemplateParser $templateParser) {
		$this->templateParser = $templateParser;
	}

	/**
	 * @param TYPO3\CMS\Extbase\Object\ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(TYPO3\CMS\Extbase\Object\ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Renders the content.
	 *
	 * @param string $partial Reference to a partial.
	 * @param array $arguments Arguments to pass to the partial.
	 * @api
	 */
	public function render($partial, $arguments = array()) {
		return $this->renderTemplate('Partials/' . $partial, $arguments);
	}

	/**
	 * Build the rendering context
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	protected function buildRenderingContext($templateVariables) {
		$variableContainer = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_TemplateVariableContainer', $templateVariables);

		$renderingContext = $this->objectManager->create('Tx_Fluid_Core_Rendering_RenderingContext');
		$viewHelperVariableContainer = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer');
		if (method_exists($renderingContext, 'setTemplateVariableContainer')) {
			$renderingContext->setTemplateVariableContainer($variableContainer);
			$renderingContext->setViewHelperVariableContainer($viewHelperVariableContainer);
		} else {
			$renderingContext->injectTemplateVariableContainer($variableContainer);
			$renderingContext->injectViewHelperVariableContainer($viewHelperVariableContainer);
		}
		return $renderingContext;
	}

	/**
	 *
	 * @param string $filePath
	 * @param Array $variables
	 */
	protected function renderTemplate($filePath, $variables) {
		if (!isset($variables['settings']['codeTemplateRootPath'])) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Render: ' . $filePath, 'builder', 2, $variables);
			throw new Exception('No template root path configured: ' . $filePath);
		}
		if (!file_exists($variables['settings']['codeTemplateRootPath'] . $filePath)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('No template file found: ' . $variables['settings']['codeTemplateRootPath'] . $filePath, 'extension_builder', 2, $variables);
			throw new Exception('No template file found: ' . $variables['settings']['codeTemplateRootPath'] . $filePath);
		}
		$parsedTemplate = $this->templateParser->parse(file_get_contents($variables['settings']['codeTemplateRootPath'] . $filePath));
		return $parsedTemplate->render($this->buildRenderingContext($variables));
	}
}

?>