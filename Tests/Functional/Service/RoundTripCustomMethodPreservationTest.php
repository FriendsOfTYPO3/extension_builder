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
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

/**
 * Regression tests for GitHub issue #736:
 * Custom methods in Model/Controller/Repository must be preserved when
 * roundtripping with overwriteSettings Classes: merge.
 */
class RoundTripCustomMethodPreservationTest extends BaseFunctionalTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());
    }

    private function writePhpClassWithCustomMethod(string $dir, string $fileName, string $namespace, string $className, string $customMethodName): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents(
            $dir . $fileName,
            "<?php\nnamespace {$namespace};\nclass {$className} {\n"
            . "    public function generatedMethod(): void {}\n"
            . "    public function {$customMethodName}(): string { return 'custom'; }\n"
            . "}\n"
        );
    }

    /**
     * @test
     */
    public function customMethodInModelIsPreservedOnRoundtrip(): void
    {
        $modelName = 'RoundtripModel';
        $uid = md5('roundtrip-model-custom-method');

        $modelDir = $this->extension->getExtensionDir() . 'Classes/Domain/Model/';
        $this->writePhpClassWithCustomMethod(
            $modelDir,
            $modelName . '.php',
            'EBT\\Dummy\\Domain\\Model',
            $modelName,
            'myCustomBusinessMethod'
        );

        $domainObject = $this->buildDomainObject($modelName);
        $domainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);

        $existingClassFile = $this->roundTripService->getDomainModelClassFile($domainObject);

        self::assertNotNull($existingClassFile, 'getDomainModelClassFile must return the existing file');
        self::assertTrue(
            $existingClassFile->getFirstClass()->methodExists('myCustomBusinessMethod'),
            'Custom method must be present in the parsed class file'
        );

        $property = new StringProperty('title');
        $property->setUniqueIdentifier(md5('title-prop'));
        $domainObject->addProperty($property);

        $updatedClassFile = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            $existingClassFile
        );

        $updatedClass = $updatedClassFile->getFirstClass();
        self::assertTrue(
            $updatedClass->methodExists('myCustomBusinessMethod'),
            'Custom method must still be present after regeneration via ClassBuilder'
        );
        self::assertTrue(
            $updatedClass->methodExists('getTitle'),
            'Generated getter for new property must be present'
        );
    }

    /**
     * @test
     */
    public function customMethodInControllerIsPreservedOnRoundtrip(): void
    {
        $modelName = 'RoundtripController';
        $uid = md5('roundtrip-controller-custom-method');

        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        $this->writePhpClassWithCustomMethod(
            $controllerDir,
            $modelName . 'Controller.php',
            'EBT\\Dummy\\Controller',
            $modelName . 'Controller',
            'myCustomHelperMethod'
        );

        $domainObject = $this->buildDomainObject($modelName);
        $domainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);

        $existingClassFile = $this->roundTripService->getControllerClassFile($domainObject);

        self::assertNotNull($existingClassFile, 'getControllerClassFile must return the existing file');
        self::assertTrue(
            $existingClassFile->getFirstClass()->methodExists('myCustomHelperMethod'),
            'Custom method must be present in the parsed controller class file'
        );

        $controllerTemplatePath = $this->codeTemplateRootPath . 'Classes/Controller/Controller.phpt';
        $updatedClassFile = $this->classBuilder->generateControllerClassFileObject(
            $domainObject,
            $controllerTemplatePath,
            $existingClassFile
        );

        $updatedClass = $updatedClassFile->getFirstClass();
        self::assertTrue(
            $updatedClass->methodExists('myCustomHelperMethod'),
            'Custom method must still be present after regeneration via ClassBuilder'
        );
    }

    /**
     * @test
     */
    public function customMethodInRepositoryIsPreservedOnRoundtrip(): void
    {
        $modelName = 'RoundtripRepository';
        $uid = md5('roundtrip-repository-custom-method');

        $repositoryDir = $this->extension->getExtensionDir() . 'Classes/Domain/Repository/';
        $this->writePhpClassWithCustomMethod(
            $repositoryDir,
            $modelName . 'Repository.php',
            'EBT\\Dummy\\Domain\\Repository',
            $modelName . 'Repository',
            'findByCustomCriteria'
        );

        $domainObject = $this->buildDomainObject($modelName, true, true);
        $domainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);

        $existingClassFile = $this->roundTripService->getRepositoryClassFile($domainObject);

        self::assertNotNull($existingClassFile, 'getRepositoryClassFile must return the existing file');
        self::assertTrue(
            $existingClassFile->getFirstClass()->methodExists('findByCustomCriteria'),
            'Custom method must be present in the parsed repository class file'
        );

        $repositoryTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Repository/Repository.phpt';
        $updatedClassFile = $this->classBuilder->generateRepositoryClassFileObject(
            $domainObject,
            $repositoryTemplatePath,
            $existingClassFile
        );

        $updatedClass = $updatedClassFile->getFirstClass();
        self::assertTrue(
            $updatedClass->methodExists('findByCustomCriteria'),
            'Custom method must still be present after regeneration via ClassBuilder'
        );
    }
}
