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
     * @transient
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
     * @return void
     */
    public function setName($name)
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
     * @return void
     */
    public function setRole($role)
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
     * @return void
     */
    public function setEmail($email)
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
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}
