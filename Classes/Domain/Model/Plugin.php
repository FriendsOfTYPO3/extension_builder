<?php
namespace EBT\ExtensionBuilder\Domain\Model;

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

class Plugin
{
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $type = '';
    /**
     * @var string
     */
    protected $key = '';
    /**
     * array('controller' => 'MyController', 'actions' => 'action1,action2')
     *
     * @var string[]
     */
    protected $controllerActionCombinations = array();
    /**
     * array('controller' => 'MyController', 'actions' => 'action1,action2')
     *
     * @var string[]
     */
    protected $noncacheableControllerActions = array();
    /**
     * @var string[]
     */
    protected $switchableControllerActions = array();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $key
     * @return void
     */
    public function setKey($key)
    {
        $this->key = strtolower($key);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param array $controllerActionCombinations
     * @return void
     */
    public function setControllerActionCombinations(array $controllerActionCombinations)
    {
        $this->controllerActionCombinations = $controllerActionCombinations;
    }

    /**
     * Used in fluid templates for localconf.php
     * if controllerActionCombinations are empty we have to
     * return null to enable test in condition
     *
     * @return array|null
     */
    public function getControllerActionCombinations()
    {
        if (empty($this->controllerActionCombinations)) {
            return null;
        }
        return $this->controllerActionCombinations;
    }

    /**
     * @param array $noncacheableControllerActions
     * @return void
     */
    public function setNoncacheableControllerActions(array $noncacheableControllerActions)
    {
        $this->noncacheableControllerActions = $noncacheableControllerActions;
    }

    /**
     * @return array
     */
    public function getNoncacheableControllerActions()
    {
        if (empty($this->noncacheableControllerActions)) {
            return null;
        }
        return $this->noncacheableControllerActions;
    }

    /**
     * @param array $switchableControllerActions
     * @return void
     */
    public function setSwitchableControllerActions($switchableControllerActions)
    {
        $this->switchableControllerActions = $switchableControllerActions;
    }

    /**
     * @return bool
     */
    public function getSwitchableControllerActions()
    {
        return $this->switchableControllerActions;
    }
}
