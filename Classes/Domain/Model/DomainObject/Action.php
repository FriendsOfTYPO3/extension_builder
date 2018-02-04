<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

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

use EBT\ExtensionBuilder\Domain\Model\DomainObject;

/**
 * An action defined for a domain object
 *
 */
class Action
{
    /**
     * the action's name
     *
     * @var string
     */
    protected $name = '';
    /**
     * the domain object this action belongs to
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected $domainObject = null;
    /**
     * Is a template required for this action?
     *
     * @var bool
     */
    protected $needsTemplate = false;
    /**
     * Is a form required in the template for this action?
     *
     * @var bool
     */
    protected $needsForm = false;
    /**
     * Is a property partial required in the template for this action?
     *
     * @var bool
     */
    protected $needsPropertyPartial = false;
    /**
     * these actions do not need a template since they are never rendered
     *
     * @var string[]
     */
    protected $actionNamesWithNoRendering = [
        'create',
        'update',
        'delete'
    ];
    /**
     * these actions need a form
     *
     * @var string[]
     */
    protected $actionNamesWithForm = [
        'new',
        'edit'
    ];
    /**
     * these actions should not be cached
     *
     * @var string[]
     */
    protected $actionNamesThatShouldNotBeCached = [
        'create',
        'update',
        'delete'
    ];
    /**
     * flag: true if the action is cacheable
     *
     * @var bool|null
     */
    protected $cacheable = null;

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * DO NOT CALL DIRECTLY! This is being called by addAction() automatically.
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject the domain object this actions belongs to
     */
    public function setDomainObject(DomainObject $domainObject)
    {
        $this->domainObject = $domainObject;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    public function getDomainObject()
    {
        return $this->domainObject;
    }

    /**
     * Is a template required for this action?
     *
     * @return bool
     */
    public function getNeedsTemplate()
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
    public function getNeedsForm()
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
    public function getNeedsPropertyPartial()
    {
        if ($this->getName() == 'show') {
            $this->needsPropertyPartial = true;
        } else {
            $this->needsPropertyPartial = false;
        }
        return $this->needsPropertyPartial;
    }

    /**
     * setter for cacheable flag
     *
     * @param bool $cacheable
     */
    public function setCacheable($cacheable)
    {
        $this->cacheable = $cacheable;
    }

    /**
     * Getter for cacheable
     *
     * @return bool|null $cacheable
     */
    public function getCacheable()
    {
        return $this->isCacheable();
    }

    /**
     * should this action be cacheable
     *
     * @return bool
     */
    public function isCacheable()
    {
        if (!isset($this->cacheable)) {
            $this->cacheable = !in_array($this->getName(), $this->actionNamesThatShouldNotBeCached);
        }
        return $this->cacheable;
    }
}
