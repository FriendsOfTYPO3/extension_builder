<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Case view helper that is only usable within the SwitchViewHelper.
 * @see \TYPO3\CMS\Fluid\ViewHelpers\SwitchViewHelper
 *
 * @api
 */
class CaseViewHelper extends AbstractViewHelper
{

    /**
    * Arguments Initialization
    */
    public function initializeArguments() {
       $this->registerArgument('value', 'mixed', 'The switch value. If it matches, the child will be rendered', FALSE);
       $this->registerArgument('default', 'boolean', 'If this is set, this child will be rendered, if none else matches', FALSE);
    }

    /**
     *
     * @return string the contents of this view helper if $value equals the expression of the surrounding switch view helper, or $default is true. otherwise an empty string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     *
     * @api
     */
    public function render()
    {
        $default = false;
        $value = null;
        if (!$this->hasArgument('default')) {
            $default = $this->arguments['default'];
        }
        if (!$this->hasArgument('value')) {
            $value = $this->arguments['value'];
        }

        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        if (!$viewHelperVariableContainer->exists('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'switchExpression')) {
            throw new Exception('The case View helper can only be used within a switch View helper', 1368112037);
        }
        if (is_null($value) && $default === false) {
            throw new Exception('The case View helper must have either value or default argument', 1382867521);
        }
        $switchExpression = $viewHelperVariableContainer->get('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'switchExpression');

        // non-type-safe comparison by intention
        if ($default === true || $switchExpression == $this->arguments['value']) {
            $viewHelperVariableContainer->addOrUpdate('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'break', true);
            return $this->renderChildren();
        }
        return '';
    }
}
