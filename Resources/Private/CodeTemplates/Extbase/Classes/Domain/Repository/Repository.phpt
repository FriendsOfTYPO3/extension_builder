<?php

declare(strict_types=1);

namespace VENDOR\Package\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * DomainObject Repository
 */
class DomainObjectRepository extends Repository
{

    protected array $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];

}
