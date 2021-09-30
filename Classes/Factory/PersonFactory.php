<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Factory;

use EBT\ExtensionBuilder\Domain\Model\Person;

class PersonFactory
{
    public function buildPerson(array $properties): Person
    {
        return (new Person())
            ->setName($properties['name'])
            ->setRole($properties['role'])
            ->setEmail($properties['email'])
            ->setCompany($properties['company']);
    }
}
