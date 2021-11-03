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

class Plugin
{
    protected string $name = '';
    protected string $description = '';
    protected string $key = '';
    /**
     * ['controller' => 'MyController', 'actions' => 'action1,action2']
     *
     * @var string[]
     */
    protected array $controllerActionCombinations = [];
    /**
     * ['controller' => 'MyController', 'actions' => 'action1,action2']
     *
     * @var string[]
     */
    protected array $nonCacheableControllerActions = [];

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setKey(string $key): void
    {
        $this->key = strtolower($key);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setControllerActionCombinations(array $controllerActionCombinations): void
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
    public function getControllerActionCombinations(): ?array
    {
        if (empty($this->controllerActionCombinations)) {
            return null;
        }
        return $this->controllerActionCombinations;
    }

    public function setNonCacheableControllerActions(array $nonCacheableControllerActions): void
    {
        $this->nonCacheableControllerActions = $nonCacheableControllerActions;
    }

    public function getNonCacheableControllerActions(): ?array
    {
        if (empty($this->nonCacheableControllerActions)) {
            return null;
        }
        return $this->nonCacheableControllerActions;
    }
}
