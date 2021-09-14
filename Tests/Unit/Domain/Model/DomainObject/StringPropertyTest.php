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

namespace EBT\ExtensionBuilder\Tests\Unit\Domain\Model\DomainObject;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

class StringPropertyTest extends BaseUnitTest
{
    /**
     * @test
     */
    public function propertyRenamesFieldIfItMatchesReservedWord(): void
    {
        $domainObject = $this->buildDomainObject('SomeModel', true, true);

        $property = new StringProperty();
        $property->setName('Order');
        $property->setDomainObject($domainObject);
        self::assertEquals('tx_dummy_order', $property->getFieldName());
    }
}
