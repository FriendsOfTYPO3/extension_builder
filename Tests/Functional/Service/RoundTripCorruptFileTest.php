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

use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripCorruptFileTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function getDomainModelClassFileReturnsNullForCorruptPhpFile(): void
    {
        $modelName = 'CorruptModel';
        $modelClassDir = $this->extension->getExtensionDir() . 'Classes/Domain/Model/';
        mkdir($modelClassDir, 0777, true);

        file_put_contents($modelClassDir . $modelName . '.php', '<?php this is not valid php {{{');

        $domainObject = $this->buildDomainObject($modelName);
        $uid = md5('corrupt-model');
        $domainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $result = $this->roundTripService->getDomainModelClassFile($domainObject);

        self::assertNull($result, 'Expected null for corrupt PHP file, got a File object');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getControllerClassFileReturnsNullForCorruptPhpFile(): void
    {
        $modelName = 'CorruptController';
        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        mkdir($controllerDir, 0777, true);

        file_put_contents($controllerDir . $modelName . 'Controller.php', '<?php class { broken');

        $domainObject = $this->buildDomainObject($modelName);
        $uid = md5('corrupt-controller');
        $domainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $result = $this->roundTripService->getControllerClassFile($domainObject);

        self::assertNull($result, 'Expected null for corrupt controller PHP file, got a File object');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getRepositoryClassFileReturnsNullForCorruptPhpFile(): void
    {
        $modelName = 'CorruptRepository';
        $repositoryDir = $this->extension->getExtensionDir() . 'Classes/Domain/Repository/';
        mkdir($repositoryDir, 0777, true);

        file_put_contents($repositoryDir . $modelName . 'Repository.php', '<?php syntax error here !!!');

        $domainObject = $this->buildDomainObject($modelName);
        $uid = md5('corrupt-repository');
        $domainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $result = $this->roundTripService->getRepositoryClassFile($domainObject);

        self::assertNull($result, 'Expected null for corrupt repository PHP file, got a File object');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function corruptModelFileAddsParseWarning(): void
    {
        $modelName = 'CorruptModelWarn';
        $modelClassDir = $this->extension->getExtensionDir() . 'Classes/Domain/Model/';
        mkdir($modelClassDir, 0777, true);

        $filePath = $modelClassDir . $modelName . '.php';
        file_put_contents($filePath, '<?php this is not valid php {{{');

        $domainObject = $this->buildDomainObject($modelName);
        $uid = md5('corrupt-model-warn');
        $domainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $domainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $this->roundTripService->getDomainModelClassFile($domainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertNotEmpty($warnings, 'Expected a parse warning to be recorded');
        self::assertStringContainsString($modelName . '.php', $warnings[0]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getDomainModelClassFileContinuesForOtherObjectsAfterCorruptFile(): void
    {
        $corruptName = 'CorruptModelA';
        $validName = 'ValidModelB';
        $modelClassDir = $this->extension->getExtensionDir() . 'Classes/Domain/Model/';
        mkdir($modelClassDir, 0777, true);

        // Write corrupt file for first domain object
        file_put_contents($modelClassDir . $corruptName . '.php', '<?php this is broken {{{');

        // Write a valid class file for second domain object
        $validContent = "<?php\nnamespace EBT\\Dummy\\Domain\\Model;\nclass ValidModelB extends \\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity {}";
        file_put_contents($modelClassDir . $validName . '.php', $validContent);

        $corruptDomainObject = $this->buildDomainObject($corruptName);
        $corruptUid = md5('corrupt-a');
        $corruptDomainObject->setUniqueIdentifier($corruptUid);

        $validDomainObject = $this->buildDomainObject($validName);
        $validUid = md5('valid-b');
        $validDomainObject->setUniqueIdentifier($validUid);

        $this->roundTripService->_set('previousDomainObjects', [
            $corruptUid => $corruptDomainObject,
            $validUid => $validDomainObject,
        ]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $corruptResult = $this->roundTripService->getDomainModelClassFile($corruptDomainObject);
        $validResult = $this->roundTripService->getDomainModelClassFile($validDomainObject);

        self::assertNull($corruptResult, 'Corrupt file should return null');
        self::assertNotNull($validResult, 'Valid file should return a File object');
    }
}
