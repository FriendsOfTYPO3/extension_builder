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
class Tx_ExtbaseKickstarter_ViewHelpers_RenderViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	/**
	 *
	 * @var Tx_Fluid_Core_Parser_TemplateParser
	 */
	protected $templateParser;

	/**
	 *
	 * @var Tx_Fluid_Compatibility_ObjectManager
	 */
	protected $objectManager;

	public function __construct() {
		$this->templateParser = Tx_Fluid_Compatibility_TemplateParserBuilder::build();
		$this->objectManager = new Tx_Fluid_Compatibility_ObjectManager();
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
		$renderingContext->setTemplateVariableContainer($variableContainer);
		//$renderingContext->setControllerContext($this->controllerContext);

		$viewHelperVariableContainer = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer');
		$renderingContext->setViewHelperVariableContainer($viewHelperVariableContainer);

		return $renderingContext;
	}

	protected function renderTemplate($filePath, $variables) {
		$parsedTemplate = $this->templateParser->parse(file_get_contents(t3lib_extMgm::extPath('extbase_kickstarter').'Resources/Private/CodeTemplates/' . $filePath));
		return $parsedTemplate->render($this->buildRenderingContext($variables));
	}
}


?>
