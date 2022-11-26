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
