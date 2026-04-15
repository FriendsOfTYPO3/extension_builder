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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

class SelectProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var string
     */
    protected $defaultValue = '';

    /**
     * Items for the TCA select field. Each entry is ['label' => '...', 'value' => '...'].
     *
     * @var array<int, array{label: string, value: string}>
     */
    protected array $selectItems = [];

    public function getTypeForComment(): string
    {
        return 'string';
    }

    public function getTypeHint(): string
    {
        return 'string';
    }

    public function getSqlDefinition(): string
    {
        return $this->getFieldName() . " varchar(255) NOT NULL DEFAULT '',";
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getSelectItems(): array
    {
        return $this->selectItems;
    }

    /**
     * @param array<int, array{label: string, value: string}> $selectItems
     */
    public function setSelectItems(array $selectItems): void
    {
        $this->selectItems = $selectItems;
    }

    public function hasSelectItems(): bool
    {
        return $this->selectItems !== [];
    }
}
