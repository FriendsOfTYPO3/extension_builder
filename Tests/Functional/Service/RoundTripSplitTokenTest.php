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

namespace EBT\ExtensionBuilder\Tests\Functional\Service;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripSplitTokenTest extends BaseFunctionalTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileGenerator->_set('roundTripEnabled', true);
        $this->extension->setSettings(array_merge(
            $this->extension->getSettings() ?? [],
            [
                'overwriteSettings' => [
                    'Classes' => [
                        'Domain' => [
                            'Model' => 'merge',
                        ],
                    ],
                ],
            ]
        ));
        $this->fileGenerator->_set('extension', $this->extension);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customCodeBelowSplitTokenSurvivesClassFileRegeneration(): void
    {
        $modelName = 'SplitTokenModel';
        $uid = md5('split-token-model');

        // Step 1: Generate the initial model class file
        $this->generateInitialModelClassFile($modelName);

        $modelPath = $this->extension->getExtensionDir() . 'Classes/Domain/Model/' . $modelName . '.php';
        self::assertFileExists($modelPath, 'Initial model file must exist');

        // Step 2: Inject a split token and custom code into the file
        $originalContent = file_get_contents($modelPath);
        $customCode = "\n// MY CUSTOM METHOD\npublic function customBehavior(): void { /* custom */ }\n";
        $contentWithSplitToken = $originalContent . RoundTrip::SPLIT_TOKEN . $customCode;
        file_put_contents($modelPath, $contentWithSplitToken);

        // Step 3: Set up old domain object (no properties)
        $oldDomainObject = $this->buildDomainObject($modelName);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // Step 4: Set up new domain object with a new property added
        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($uid);
        $newProperty = new StringProperty('description');
        $propertyUid = md5('split-token-description');
        $newProperty->setUniqueIdentifier($propertyUid);
        $newDomainObject->addProperty($newProperty);

        // Step 5: Regenerate the model class file via FileGenerator
        $this->fileGenerator->_set('extensionDirectory', $this->extension->getExtensionDir());
        $this->fileGenerator->_set('extension', $this->extension);
        $fileContents = $this->fileGenerator->generateDomainObjectCode($newDomainObject);

        // Write via the accessible writeFile method (simulates the full write pipeline)
        $this->fileGenerator->_call('writeFile', $modelPath, $fileContents);

        // Step 6: Assert custom code below split token is preserved
        $regeneratedContent = file_get_contents($modelPath);
        self::assertStringContainsString(
            'MY CUSTOM METHOD',
            $regeneratedContent,
            'Custom code below split token must survive regeneration'
        );
        self::assertStringContainsString(
            RoundTrip::SPLIT_TOKEN,
            $regeneratedContent,
            'Split token marker must still be present after regeneration'
        );

        // Step 7: Assert new property methods were generated (above the split token)
        $beforeSplitToken = explode(RoundTrip::SPLIT_TOKEN, $regeneratedContent)[0];
        self::assertStringContainsString(
            'getDescription',
            $beforeSplitToken,
            'New property getter must appear above the split token'
        );
        self::assertStringContainsString(
            'setDescription',
            $beforeSplitToken,
            'New property setter must appear above the split token'
        );
    }
}
