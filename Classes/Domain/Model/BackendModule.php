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

class BackendModule
{
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $description = '';
    /**
     * @var string
     */
    protected $tabLabel = '';
    /**
     * The mainModule of the module (default is 'web')
     *
     * @var string
     */
    protected $mainModule = 'web';
    /**
     * @var string
     */
    protected $key = '';
    /**
     * array with configuration arrays
     *
     * array('controller' => 'MyController', 'actions' => 'action1,action2')
     *
     * @var string[]
     */
    protected $controllerActionCombinations = [];

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->tabLabel;
    }

    /**
     * @param string $tabLabel
     * @return void
     */
    public function setTabLabel($tabLabel)
    {
        $this->tabLabel = $tabLabel;
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
     * @return string key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param $mainModule
     * @return void
     */
    public function setMainModule($mainModule)
    {
        $this->mainModule = $mainModule;
    }

    /**
     * @return string
     */
    public function getMainModule()
    {
        return $this->mainModule;
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
     * @return array
     */
    public function getControllerActionCombinations()
    {
        return $this->controllerActionCombinations;
    }
}
