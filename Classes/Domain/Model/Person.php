<?php

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

/**
 * A person participating in the project somehow (i.e. as a developer).
 */
class Person
{
    /**
     * TODO make that work
     * This Array contains all valid values for the role of a person.
     * Extend here and in the locallang (mlang_Tx_ExtensionBuilder_domain_model_person_[rolekey from array]) to add new Roles.
     *
     * @var string[]
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     */
    protected static $ROLES = ['developer', 'product_manager'];
    /**
     * @var string
     */
    protected $name = '';
    /**
     * TODO validation?
     *
     * @var string
     * @see \EBT\ExtensionBuilder\Domain\Model\Person::ROLES
     */
    protected $role = '';
    /**
     * @var string
     */
    protected $email = '';
    /**
     * @var string
     */
    protected $company = '';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }
}
