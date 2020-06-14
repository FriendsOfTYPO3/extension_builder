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

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Switch view helper which can be used to render content depending on a value or expression.
 * Implements what a basic switch()-PHP-method does.
 *
 * = Examples =
 *
 * <code title="Simple Switch statement">
 * <f:switch expression="{person.gender}">
 *   <f:case value="male">Mr.</f:case>
 *   <f:case value="female">Mrs.</f:case>
 *   <f:case default="true">Mrs. or Mr.</f:case>
 * </f:switch>
 * </code>
 * <output>
 * Mr. / Mrs. (depending on the value of {person.gender}) or if no value evaluates to true, default case
 * </output>
 *
 * Note: Using this view helper can be a sign of weak architecture. If you end up using it extensively
 * you might want to consider restructuring your controllers/actions and/or use partials and sections.
 * E.g. the above example could be achieved with <f:render partial="title.{person.gender}" /> and the partials
 * "title.male.html", "title.female.html", ...
 * Depending on the scenario this can be easier to extend and possibly contains less duplication.
 *
 * @api
 * @deprecated Use default `f:switch` instead
 */
class SwitchViewHelper extends AbstractViewHelper
{
    /**
     * An array of AbstractNode items
     * @var array
     */
    protected $childNodes = [];
    /**
     * @var mixed
     */
    protected $backupSwitchExpression;
    /**
     * @var bool
     */
    protected $backupBreakState = false;

    /**
     * Setter for ChildNodes
     *
     * @param array $childNodes Child nodes of this syntax tree node
     * @return void
     */
    public function setChildNodes(array $childNodes)
    {
        $this->childNodes = $childNodes;
    }

    /**
     * Arguments Initialization
     */
    public function initializeArguments()
    {
        $this->registerArgument('expression', 'mixed', '', true);
    }

    /**
     * @return string the rendered string
     * @api
     */
    public function render()
    {
        $content = '';
        $this->backupSwitchState();
        $templateVariableContainer = $this->renderingContext->getViewHelperVariableContainer();

        $templateVariableContainer->addOrUpdate(
            __CLASS__,
            'switchExpression',
            $this->arguments['expression']
        );
        $templateVariableContainer->addOrUpdate(
            __CLASS__,
            'break',
            false
        );

        foreach ($this->childNodes as $childNode) {
            if (
                !$childNode instanceof ViewHelperNode
                || $childNode->getViewHelperClassName() !== CaseViewHelper::class
            ) {
                continue;
            }
            $content = $childNode->evaluate($this->renderingContext);
            if ($templateVariableContainer->get(
                    __CLASS__,
                    'break') === true) {
                break;
            }
        }

        $templateVariableContainer->remove(__CLASS__, 'switchExpression');
        $templateVariableContainer->remove(__CLASS__, 'break');

        $this->restoreSwitchState();
        return $content;
    }

    /**
     * Backups "switch expression" and "break" state of a possible parent switch ViewHelper to support nesting
     *
     * @return void
     */
    protected function backupSwitchState()
    {
        if ($this->renderingContext->getViewHelperVariableContainer()->exists(
            __CLASS__,
            'switchExpression')
        ) {
            $this->backupSwitchExpression = $this->renderingContext->getViewHelperVariableContainer()->get(
                __CLASS__,
                'switchExpression'
            );
        }
        if ($this->renderingContext->getViewHelperVariableContainer()->exists(
            __CLASS__,
            'break')
        ) {
            $this->backupBreakState = $this->renderingContext->getViewHelperVariableContainer()->get(
                __CLASS__,
                'break'
            );
        }
    }

    /**
     * Restores "switch expression" and "break" states that might have been backed up in backupSwitchState() before
     *
     * @return void
     */
    protected function restoreSwitchState()
    {
        if ($this->backupSwitchExpression !== null) {
            $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(
                __CLASS__,
                'switchExpression',
                $this->backupSwitchExpression
            );
        }
        if ($this->backupBreakState !== false) {
            $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(
                __CLASS__,
                'break',
                true
            );
        }
    }
}
