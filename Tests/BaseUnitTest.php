<?php
namespace EBT\ExtensionBuilder\Tests;

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

use EBT\ExtensionBuilder\Utility\Spyc;
use org\bovigo\vfs\vfsStream;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class BaseUnitTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = false;

    /**
     * @var string
     */
    protected $modelClassDir = 'Classes/Domain/Model/';
    /**
     * @var string
     */
    protected $codeTemplateRootPath = '';
    /**
     * @var string
     */
    protected $modelClassTemplatePath = '';
    /**
     * @var string
     */
    protected $fixturesPath = '';
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;

    protected function setUp()
    {
        parent::setUp();

        $this->fixturesPath = __DIR__ . '/Fixtures/';

        $testTargetDir = 'testDir';
        vfsStream::setup($testTargetDir);
        $dummyExtensionDir = vfsStream::url($testTargetDir) . '/';

        $settings = Spyc::YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

        $this->extension = $this->getMock(\EBT\ExtensionBuilder\Domain\Model\Extension::class, array('getExtensionDir'));
        $this->extension->setVendorName('EBT');
        $this->extension->setExtensionKey('dummy');
        $this->extension->expects(
            self::any())
            ->method('getExtensionDir')
            ->will(self::returnValue($dummyExtensionDir));
        if (is_dir($dummyExtensionDir)) {
            GeneralUtility::mkdir($dummyExtensionDir);
        }
        $this->extension->setSettings($settings);

        $this->codeTemplateRootPath = PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
        $this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';
    }

    protected function tearDown()
    {
        if (!empty($this->extension) && $this->extension->getExtensionKey() != null) {
            GeneralUtility::rmdir($this->extension->getExtensionDir(), true);
        }

        parent::tearDown();
    }

    /**
     * Helper function
     * @param $name
     * @param $entity
     * @param $aggregateRoot
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected function buildDomainObject($name, $entity = false, $aggregateRoot = false)
    {
        $domainObject = $this->getAccessibleMock(
            \EBT\ExtensionBuilder\Domain\Model\DomainObject::class,
            array('dummy')
        );
        /* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
        $domainObject->setExtension($this->extension);
        $domainObject->setName($name);
        $domainObject->setEntity($entity);
        $domainObject->setAggregateRoot($aggregateRoot);
        if ($aggregateRoot) {
            $defaultActions = ['list', 'show', 'new', 'create', 'edit', 'update', 'delete'];
            foreach ($defaultActions as $actionName) {
                $action = GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Action');
                $action->setName($actionName);
                if ($actionName == 'deleted') {
                    $action->setNeedsTemplate = false;
                }
                $domainObject->addAction($action);
            }
        }
        return $domainObject;
    }
}
