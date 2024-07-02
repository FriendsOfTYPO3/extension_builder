<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
/**
 * This file is part of the "Extension Builder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 */
/**
 * The repository for Mains
 */
class MainRepository extends Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['sorting' => QueryInterface::ORDER_ASCENDING];
}
