<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Sebastian Gebhard <sebastian.gebhard@gmail.com>
 *  (c) 2013 Nico de Haen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * An action defined for a domain object
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Action {
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
	protected $domainObject = NULL;

	/**
	 * Is a template required for this action?
	 *
	 * @var bool
	 */
	protected $needsTemplate = FALSE;

	/**
	 * Is a form required in the template for this action?
	 *
	 * @var bool
	 */
	protected $needsForm = FALSE;

	/**
	 * Is a property partial required in the template for this action?
	 *
	 * @var bool
	 */
	protected $needsPropertyPartial = FALSE;

	/**
	 * these actions do not need a template since they are never rendered
	 *
	 * @var string[]
	 */
	protected $actionNamesWithNoRendering = array(
		'create',
		'update',
		'delete'
	);

	/**
	 * these actions need a form
	 *
	 * @var string[]
	 */
	protected $actionNamesWithForm = array(
		'new',
		'edit'
	);

	/**
	 * these actions should not be cached
	 *
	 * @var string[]
	 */
	protected $actionNamesThatShouldNotBeCached = array(
		'create',
		'update',
		'delete'
	);

	/**
	 * flag: TRUE if the action is cacheable
	 *
	 * @var bool|NULL
	 */
	protected $cacheable = NULL;

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addAction() automatically.
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject the domain object this actions belongs to
	 */
	public function setDomainObject(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject) {
		$this->domainObject = $domainObject;
	}

	/**
	 *
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
	 */
	public function getDomainObject() {
		return $this->domainObject;
	}

	/**
	 * Is a template required for this action?
	 *
	 * @return boolean
	 */
	public function getNeedsTemplate() {
		if (in_array($this->getName(), $this->actionNamesWithNoRendering)) {
			$this->needsTemplate = FALSE;
		}
		else {
			$this->needsTemplate = TRUE;
		}
		return $this->needsTemplate;
	}

	/**
	 * Is a form required to render the actions template?
	 *
	 * @return boolean
	 */
	public function getNeedsForm() {
		if (in_array($this->getName(), $this->actionNamesWithForm)) {
			$this->needsForm = TRUE;
		}
		else {
			$this->needsForm = FALSE;
		}
		return $this->needsForm;
	}

	/**
	 * Is a property partial needed to render the actions template?
	 *
	 * @return boolean
	 */
	public function getNeedsPropertyPartial() {
		if ($this->getName() == 'show') {
			$this->needsPropertyPartial = TRUE;
		}
		else {
			$this->needsPropertyPartial = FALSE;
		}
		return $this->needsPropertyPartial;
	}

	/**
	 * setter for cacheable flag
	 *
	 * @param boolean $cacheable
	 */
	public function setCacheable($cacheable) {
		$this->cacheable = $cacheable;
	}

	/**
	 * Getter for cacheable
	 *
	 * @return boolean|NULL $cacheable
	 */
	public function getCacheable() {
		return $this->isCacheable();
	}

	/**
	 * should this action be cacheable
	 *
	 * @return boolean
	 */
	public function isCacheable() {
		if (!isset($this->cacheable)) {
			$this->cacheable = !in_array($this->getName(), $this->actionNamesThatShouldNotBeCached);
		}
		return $this->cacheable;
	}
}
