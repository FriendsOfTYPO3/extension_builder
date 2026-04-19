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

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\ViewHelpers\CopyrightViewHelper;

class CopyrightViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
    {
        return [
            'minimalCopyRight' => [
                '2021',
                [
                    (new Person())->setName('John Doe'),
                ],
                ' * (c) 2021 John Doe',
            ],
            'copyRightWithEmail' => [
                '2021',
                [
                    (new Person())->setName('John Doe')->setEmail('john@doe.com'),
                ],
                ' * (c) 2021 John Doe <john@doe.com>',
            ],
            'copyRightWithCompany' => [
                '2021',
                [
                    (new Person())->setName('John Doe')->setCompany('Doe GmbH'),
                ],
                ' * (c) 2021 John Doe, Doe GmbH',
            ],
            'fullCopyRight' => [
                '2021',
                [
                    (new Person())->setName('John Doe')->setEmail('john@doe.com')->setCompany('Doe GmbH'),
                ],
                ' * (c) 2021 John Doe <john@doe.com>, Doe GmbH',
            ],
            'fullCopyRightWithMultiplePersons' => [
                '2021',
                [
                    (new Person())->setName('John Doe')->setEmail('john@doe.com')->setCompany('Doe GmbH'),
                    (new Person())->setName('Richard Roe')->setEmail('richard@roe.com')->setCompany('Roe AG'),
                    (new Person())->setName('Taylor Shaw')->setEmail('taylor@shaw.org')->setCompany('Shaw Ltd.'),
                ],
                ' * (c) 2021 John Doe <john@doe.com>, Doe GmbH' . "\n"
                . ' *          Richard Roe <richard@roe.com>, Roe AG' . "\n"
                . ' *          Taylor Shaw <taylor@shaw.org>, Shaw Ltd.',
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function render(string $date, array $persons, string $expected): void
    {
        $viewHelper = new CopyrightViewHelper();

        $this->arguments = [
            'date' => $date,
            'persons' => $persons,
        ];

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
