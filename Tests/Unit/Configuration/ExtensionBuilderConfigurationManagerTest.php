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

namespace EBT\ExtensionBuilder\Tests\Unit\Configuration;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

class ExtensionBuilderConfigurationManagerTest extends BaseUnitTest
{
    private ExtensionBuilderConfigurationManager $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            ExtensionBuilderConfigurationManager::class,
            null,
            [],
            '',
            false
        );
    }

    /**
     * @test
     */
    public function mapAdvancedModeToleratesRelationWithoutRenderType(): void
    {
        $jsonConfig = [
            [
                'value' => [
                    'relationGroup' => [
                        'relations' => [
                            [
                                'relationType' => 'manyToOne',
                                'propertyIsExcludeField' => true,
                                'lazyLoading' => false,
                                'relationDescription' => '',
                                'foreignRelationClass' => '',
                                // renderType intentionally omitted — old ExtensionBuilder.json
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->subject->_call('mapAdvancedMode', $jsonConfig, true);

        self::assertArrayHasKey('renderType', $result[0]['value']['relationGroup']['relations'][0]['advancedSettings']);
        self::assertNull($result[0]['value']['relationGroup']['relations'][0]['advancedSettings']['renderType']);
    }

    /**
     * @test
     */
    public function mapAdvancedModeBackwardToleratesMissingAdvancedSettingsKey(): void
    {
        $jsonConfig = [
            [
                'value' => [
                    'relationGroup' => [
                        'relations' => [
                            [
                                'relationType' => 'manyToOne',
                                'advancedSettings' => [
                                    'relationType' => 'manyToOne',
                                    'propertyIsExcludeField' => true,
                                    'lazyLoading' => false,
                                    'relationDescription' => '',
                                    'foreignRelationClass' => '',
                                    // renderType intentionally omitted from advancedSettings
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->subject->_call('mapAdvancedMode', $jsonConfig, false);

        self::assertArrayHasKey('renderType', $result[0]['value']['relationGroup']['relations'][0]);
        self::assertNull($result[0]['value']['relationGroup']['relations'][0]['renderType']);
    }
}
