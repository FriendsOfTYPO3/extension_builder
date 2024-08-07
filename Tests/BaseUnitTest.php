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

namespace EBT\ExtensionBuilder\Tests;

use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Utility\SpycYAMLParser;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class BaseUnitTest extends UnitTestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = false;

    protected string $modelClassDir = 'Classes/Domain/Model/';
    protected string $codeTemplateRootPath = '';
    protected string $modelClassTemplatePath = '';
    protected string $fixturesPath = '';
    protected Extension $extension;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/Fixtures/';

        $settings = SpycYAMLParser::YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

        $this->extension = $this->getMockBuilder(Extension::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $this->extension->setVendorName('EBT');

        $this->extension->setExtensionKey('dummy');
        $this->extension->setSettings($settings);

        $this->extension->setStoragePath($this->fixturesPath);

        $this->codeTemplateRootPath = Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
        $this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';
    }

    protected function tearDown(): void
    {
        if (!empty($this->extension) && $this->extension->getExtensionKey() != null) {
            GeneralUtility::rmdir($this->extension->getExtensionDir(), true);
        }
    }

    /**
     * Helper function
     *
     * @param $name
     */
    protected function buildDomainObject($name, bool $entity = false, bool $aggregateRoot = false): DomainObject
    {
        $domainObject = $this->getAccessibleMock(DomainObject::class, ['dummy']);
        $domainObject->setExtension($this->extension);
        $domainObject->setName($name);
        $domainObject->setEntity($entity);
        $domainObject->setAggregateRoot($aggregateRoot);
        if ($aggregateRoot) {
            $defaultActions = ['list', 'show', 'new', 'create', 'edit', 'update', 'delete'];
            foreach ($defaultActions as $actionName) {
                $action = GeneralUtility::makeInstance(Action::class);
                $action->setName($actionName);
                if ($actionName === 'deleted') {
                    $action->setNeedsTemplate = false;
                }
                $domainObject->addAction($action);
            }
        }
        return $domainObject;
    }
}
