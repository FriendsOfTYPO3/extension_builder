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

/**
 * Case view helper that is only usable within the SwitchViewHelper.
 * @see \TYPO3\CMS\Fluid\ViewHelpers\SwitchViewHelper
 *
 * @api
 */
class CaseViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param mixed $value The switch value. If it matches, the child will be rendered
     * @param bool $default If this is set, this child will be rendered, if none else matches
     *
     * @return string the contents of this view helper if $value equals the expression of the surrounding switch view helper, or $default is true. otherwise an empty string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     *
     * @api
     */
    public function render($value = null, $default = false)
    {
        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        if (!$viewHelperVariableContainer->exists('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'switchExpression')) {
            throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('The case View helper can only be used within a switch View helper', 1368112037);
        }
        if (is_null($value) && $default === false) {
            throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('The case View helper must have either value or default argument', 1382867521);
        }
        $switchExpression = $viewHelperVariableContainer->get('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'switchExpression');

        // non-type-safe comparison by intention
        if ($default === true || $switchExpression == $value) {
            $viewHelperVariableContainer->addOrUpdate('EBT\ExtensionBuilder\ViewHelpers\SwitchViewHelper', 'break', true);
            return $this->renderChildren();
        }
        return '';
    }
}
