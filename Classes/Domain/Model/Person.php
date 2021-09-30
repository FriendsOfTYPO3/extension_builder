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
    protected static array $ROLES = ['developer', 'product_manager'];
    protected string $name = '';
    /**
     * TODO validation?
     *
     * @see \EBT\ExtensionBuilder\Domain\Model\Person::ROLES
     */
    protected string $role = '';
    protected string $email = '';
    protected string $company = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }
}
