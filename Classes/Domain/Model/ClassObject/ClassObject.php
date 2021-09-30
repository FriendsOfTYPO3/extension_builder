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

namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

use EBT\ExtensionBuilder\Domain\Model\Container;
use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;

/**
 * Class schema representing a "PHP class" in the context of software development
 */
class ClassObject extends Container
{
    /**
     * @var Property[]
     */
    protected array $properties = [];

    /**
     * @var Method[]
     */
    protected array $methods = [];

    /**
     * @var string[]
     */
    protected array $interfaceNames = [];

    /**
     * All lines that were found below the class declaration.
     */
    protected string $appendedBlock = '';

    protected array $useTraitStatements = [];

    protected bool $isFileBased = false;

    /**
     * the path to the file this class was defined in
     */
    protected string $fileName = '';

    protected ?ClassObject $parentClass = null;

    protected ?string $parentClassName = null;

    protected bool $isTemplate = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __clone()
    {
        $clonedProperties = [];
        $clonedMethods = [];
        foreach ($this->properties as $property) {
            $clonedProperties[] = clone $property;
        }
        $this->properties = $clonedProperties;
        foreach ($this->methods as $method) {
            $clonedMethods[] = clone $method;
        }
        $this->methods = $clonedMethods;
    }

    /**
     * @param string $constantName
     * @param string $constantValue
     */
    public function setConstant($constantName, $constantValue): void
    {
        if ($constantName instanceof Node) {
            $constantName = $constantName->name;
        }
        $this->constants[$constantName] = $constantValue;
    }

    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
    }

    public function methodExists(string $methodName): bool
    {
        if (!is_array($this->methods)) {
            return false;
        }

        $methodNames = array_keys($this->methods);

        return is_array($methodNames) && in_array($methodName, $methodNames, true);
    }

    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * Allows to override an existing method.
     *
     * @param Method $classMethod
     */
    public function setMethod(Method $classMethod): void
    {
        $this->methods[$classMethod->getName()] = $classMethod;
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMethod(string $methodName): ?Method
    {
        if ($this->methodExists($methodName)) {
            return $this->methods[$methodName];
        }

        return null;
    }

    public function addMethod(Method $classMethod): self
    {
        if (!$this->methodExists($classMethod->getName())) {
            $this->methods[$classMethod->getName()] = $classMethod;
        }
        return $this;
    }

    public function removeMethod(string $methodName): bool
    {
        if ($this->methodExists($methodName)) {
            unset($this->methods[$methodName]);
            return true;
        }
        return false;
    }

    public function renameMethod(string $oldName, string $newName): bool
    {
        if ($this->methodExists($oldName)) {
            $method = $this->methods[$oldName];
            $method->setName($newName);
            $this->methods[$newName] = $method;
            $this->removeMethod($oldName);
            return true;
        }

        return false;
    }

    /**
     * Returns all methods starting with "get".
     *
     * @return Method[]
     */
    public function getGetters(): array
    {
        $getterMethods = [];
        foreach ($this->getMethods() as $method) {
            $methodName = $method->getName();
            if (strpos($methodName, 'get') === 0) {
                $propertyName = strtolower(substr($methodName, 3));
                if ($this->propertyExists($propertyName)) {
                    $getterMethods[$propertyName] = $method;
                }
            }
        }

        return $getterMethods;
    }

    /**
     * Returns all methods starting with "set".
     *
     * @return Method[]
     */
    public function getSetters(): array
    {
        $setterMethods = [];
        foreach ($this->getMethods() as $method) {
            $methodName = $method->getName();
            if (strpos($methodName, 'set') === 0) {
                $propertyName = strtolower(substr($methodName, 3));
                if ($this->propertyExists($propertyName)) {
                    $setterMethods[$propertyName] = $method;
                }
            }
        }
        return $setterMethods;
    }

    public function getProperty(string $propertyName): ?Property
    {
        if ($this->propertyExists($propertyName)) {
            if ($this->isTemplate) {
                return clone $this->properties[$propertyName];
            }

            return $this->properties[$propertyName];
        }

        return null;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function removeProperty(string $propertyName): bool
    {
        if ($this->propertyExists($propertyName)) {
            unset($this->properties[$propertyName]);
            return true;
        }
        return false;
    }

    public function renameProperty(string $oldName, string $newName): bool
    {
        if ($this->propertyExists($oldName)) {
            $property = $this->properties[$oldName];
            $property->setName($newName);
            $this->properties[$newName] = $property;
            $this->removeProperty($oldName);
            return true;
        }

        return false;
    }

    public function setPropertyTag(string $propertyName, array $tag): void
    {
        if ($this->propertyExists($propertyName)) {
            $this->properties[$propertyName]->setTag($tag['name'], $tag['value']);
        }
    }

    public function propertyExists(string $propertyName): bool
    {
        return is_array($this->methods) && in_array($propertyName, $this->getPropertyNames(), true);
    }

    public function addProperty(Property $classProperty): bool
    {
        if (!$this->propertyExists($classProperty->getName())) {
            $this->properties[$classProperty->getName()] = $classProperty;
            return true;
        }

        return false;
    }

    public function getPropertyNames(): array
    {
        return array_keys($this->properties);
    }

    public function setProperty(Property $classProperty): void
    {
        $this->properties[$classProperty->getName()] = $classProperty;
    }

    public function setParentClass(self $parentClass): void
    {
        $this->parentClass = $parentClass;
    }

    public function getParentClass(): ?self
    {
        return $this->parentClass;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getAppendedBlock(): string
    {
        return $this->appendedBlock;
    }

    public function setAppendedBlock(string $appendedBlock): void
    {
        $this->appendedBlock = $appendedBlock;
    }

    public function getInfo(): array
    {
        $infoArray = [];
        $infoArray['className'] = $this->getName();
        $infoArray['nameSpace'] = $this->getNamespaceName();
        $infoArray['parentClass'] = $this->getParentClassName();
        $infoArray['fileName'] = $this->getFileName();

        $methodArray = [];
        foreach ($this->getMethods() as $method) {
            $methodArray[$method->getName()] = [
                'parameter' => $method->getParameters()
            ];
        }
        $infoArray['Methods'] = $methodArray;
        $infoArray['Properties'] = $this->getProperties();
        $infoArray['Constants'] = $this->getConstants();
        $infoArray['Modifiers'] = $this->getModifierNames();
        $infoArray['Tags'] = $this->getTags();

        return $infoArray;
    }

    /**
     * @param string $alias
     */
    public function addAliasDeclaration($alias): void
    {
        if (!in_array($alias, $this->aliasDeclarations, true)) {
            $this->aliasDeclarations[] = $alias;
        }
    }

    public function addUseTraitStatement(TraitUse $statement): void
    {
        if (!in_array($statement, $this->useTraitStatements)) {
            $this->useTraitStatements[] = $statement;
        }
    }

    public function getUseTraitStatement(): array
    {
        return $this->useTraitStatements;
    }

    public function setInterfaceNames(array $interfaceNames): void
    {
        $this->interfaceNames = $interfaceNames;
    }

    public function getInterfaceNames(): array
    {
        return $this->interfaceNames;
    }

    public function addInterfaceName(string $interfaceName): self
    {
        if (!in_array($interfaceName, $this->interfaceNames)) {
            $this->interfaceNames[] = $interfaceName;
        }
        return $this;
    }

    public function hasInterface(string $interfaceName): bool
    {
        return in_array($interfaceName, $this->interfaceNames);
    }

    public function removeInterface(string $interfaceNameToRemove): void
    {
        $interfaceNames = [];
        foreach ($this->interfaceNames as $interfaceName) {
            if ($interfaceName != $interfaceNameToRemove) {
                $interfaceNames[] = $interfaceName;
            }
        }
        $this->interfaceNames = $interfaceNames;
    }

    public function removeAllInterfaces(): void
    {
        $this->interfaceNames = [];
    }

    public function setParentClassName(string $parentClassName): self
    {
        $this->parentClassName = $parentClassName;
        return $this;
    }

    public function getParentClassName(): ?string
    {
        return $this->parentClassName;
    }

    public function removeParentClassName(): void
    {
        $this->parentClassName = '';
    }

    public function resetAll(): void
    {
        $this->constants = [];
        $this->properties = [];
        $this->methods = [];
    }
}
