<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Domain\Model;

class BackendModule
{
    protected string $name = '';
    protected string $description = '';
    protected string $tabLabel = '';
    /**
     * The mainModule of the module (default is 'web')
     */
    protected string $mainModule = 'web';
    protected string $key = '';
    /**
     * array with configuration arrays
     *
     * array('controller' => 'MyController', 'actions' => 'action1,action2')
     *
     * @var string[]
     */
    protected array $controllerActionCombinations = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getTabLabel(): string
    {
        return $this->tabLabel;
    }

    public function setTabLabel(string $tabLabel): void
    {
        $this->tabLabel = $tabLabel;
    }

    public function setKey(string $key): void
    {
        $this->key = strtolower($key);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setMainModule(string $mainModule): void
    {
        $this->mainModule = $mainModule;
    }

    public function getMainModule(): string
    {
        return $this->mainModule;
    }

    public function setControllerActionCombinations(array $controllerActionCombinations): void
    {
        $this->controllerActionCombinations = $controllerActionCombinations;
    }

    public function getControllerActionCombinations(): array
    {
        return $this->controllerActionCombinations;
    }
}
