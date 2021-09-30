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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

use EBT\ExtensionBuilder\Domain\Model\DomainObject;

/**
 * An action defined for a domain object
 */
class Action
{
    /**
     * the action's name
     */
    protected string $name = '';
    /**
     * the domain object this action belongs to
     */
    protected ?DomainObject $domainObject = null;
    /**
     * Is a template required for this action?
     */
    protected bool $needsTemplate = false;
    /**
     * Is a form required in the template for this action?
     */
    protected bool $needsForm = false;
    /**
     * Is a property partial required in the template for this action?
     */
    protected bool $needsPropertyPartial = false;
    /**
     * these actions do not need a template since they are never rendered
     *
     * @var string[]
     */
    protected array $actionNamesWithNoRendering = [
        'create',
        'update',
        'delete'
    ];
    /**
     * these actions need a form
     *
     * @var string[]
     */
    protected array $actionNamesWithForm = [
        'new',
        'edit'
    ];
    /**
     * these actions should not be cached
     *
     * @var string[]
     */
    protected array $actionNamesThatShouldNotBeCached = [
        'create',
        'update',
        'delete'
    ];
    /**
     * flag: true if the action is cacheable
     */
    protected ?bool $cacheable = null;
    /**
     * Whether this is a custom action and needs a custom fluid template
     */
    protected bool $customAction = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addAction() automatically.
     * @param DomainObject $domainObject the domain object this actions belongs to
     */
    public function setDomainObject(DomainObject $domainObject): void
    {
        $this->domainObject = $domainObject;
    }

    public function getDomainObject(): ?DomainObject
    {
        return $this->domainObject;
    }

    /**
     * Is a template required for this action?
     *
     * @return bool
     */
    public function getNeedsTemplate(): bool
    {
        if (in_array($this->getName(), $this->actionNamesWithNoRendering)) {
            $this->needsTemplate = false;
        } else {
            $this->needsTemplate = true;
        }
        return $this->needsTemplate;
    }

    /**
     * Is a form required to render the actions template?
     *
     * @return bool
     */
    public function getNeedsForm(): bool
    {
        if (in_array($this->getName(), $this->actionNamesWithForm)) {
            $this->needsForm = true;
        } else {
            $this->needsForm = false;
        }
        return $this->needsForm;
    }

    /**
     * Is a property partial needed to render the actions template?
     *
     * @return bool
     */
    public function getNeedsPropertyPartial(): bool
    {
        if ($this->getName() === 'show') {
            $this->needsPropertyPartial = true;
        } else {
            $this->needsPropertyPartial = false;
        }
        return $this->needsPropertyPartial;
    }

    public function setCacheable(bool $cacheable): void
    {
        $this->cacheable = $cacheable;
    }

    public function getCacheable(): bool
    {
        return $this->isCacheable();
    }

    public function isCacheable(): bool
    {
        if (!isset($this->cacheable)) {
            $this->cacheable = !in_array($this->getName(), $this->actionNamesThatShouldNotBeCached);
        }
        return $this->cacheable;
    }

    public function isCustomAction(): bool
    {
        return $this->customAction;
    }

    public function setCustomAction(bool $customAction): void
    {
        $this->customAction = $customAction;
    }
}
