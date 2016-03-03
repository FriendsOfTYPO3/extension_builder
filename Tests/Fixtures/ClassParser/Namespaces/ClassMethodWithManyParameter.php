<?php
namespace EBT\Tests\Fixtures;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
 *
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

class ClassMethodWithManyParameter
{

    /**
     * This is the description
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TOOOL\Projects\Domain\Model\Calculation> $tests
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    private static function testMethod(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject, \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tests)
    {
        $number = 7;
        if ($number > $tests->count()) {
            return $domainObject;
        } else {
            $domainObject->setName('Foo');
        }
    }
}
