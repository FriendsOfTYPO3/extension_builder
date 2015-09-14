<?php
namespace EBT\ExtensionBuilder\Tests;
use org\bovigo\vfs\vfsStream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\UnknownClassException;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


abstract class BaseFunctionalTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

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
	 * @var \EBT\ExtensionBuilder\Service\Parser
	 */
	protected $parserService = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\Printer
	 */
	protected $printerService = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\ClassBuilder
	 */
	protected $classBuilder = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\RoundTrip
	 */
	protected $roundTripService = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\Extension
	 */
	protected $extension = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\FileGenerator
	 */
	protected $fileGenerator = NULL;

	protected $testExtensionsToLoad = array('typo3conf/ext/extension_builder');


	public function setUp(){
		parent::setUp();

		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		if (!class_exists('PhpParser\Parser')) {
			throw new UnknownClassException('PhpParser not found!!');
		}
		$this->fixturesPath = __DIR__ . '/Fixtures/';


		$testTargetDir = 'testDir';
		vfsStream::setup($testTargetDir);
		$dummyExtensionDir = vfsStream::url($testTargetDir) . '/';

		$yamlParser = new \EBT\ExtensionBuilder\Utility\SpycYAMLParser();
		$settings = $yamlParser->YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

		$this->extension = $this->getMock('EBT\\ExtensionBuilder\\Domain\\Model\\Extension', array('getExtensionDir'));
		$this->extension->setVendorName('EBT');
		$this->extension->setExtensionKey('dummy');
		$this->extension->expects(
			$this->any())
				->method('getExtensionDir')
				->will($this->returnValue($dummyExtensionDir));
		if (is_dir($dummyExtensionDir)) {
			GeneralUtility::mkdir($dummyExtensionDir, TRUE);
		}
		$this->extension->setSettings($settings);

		// get instances to inject in Mocks
		$configurationManager = $this->objectManager->get('EBT\\ExtensionBuilder\\Configuration\\ConfigurationManager');

		$this->parserService = new \EBT\ExtensionBuilder\Service\Parser(new \PhpParser\Lexer());
		$this->printerService = $this->objectManager->get('EBT\\ExtensionBuilder\\Service\Printer');
		$localizationService = $this->objectManager->get('EBT\\ExtensionBuilder\\Service\\LocalizationService');

		$this->classBuilder = $this->objectManager->get('EBT\\ExtensionBuilder\\Service\\ClassBuilder');
		$this->classBuilder->initialize($this->extension);

		$this->roundTripService = $this->getMock($this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Service\\RoundTrip'), array('dummy'));
		$this->inject($this->roundTripService, 'configurationManager', $configurationManager);
		$this->inject($this->roundTripService, 'parserService', $this->parserService);
		$this->roundTripService->initialize($this->extension);

		$this->fileGenerator = $this->getMock($this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Service\\FileGenerator'), array('dummy'));
		$this->inject($this->fileGenerator, 'objectManager',$this->objectManager);
		$this->inject($this->fileGenerator,'printerService',$this->printerService);
		$this->inject($this->fileGenerator, 'localizationService',$localizationService);
		$this->inject($this->fileGenerator,'classBuilder',$this->classBuilder);

		$this->inject($this->fileGenerator, 'roundTripService', $this->roundTripService);

		$this->codeTemplateRootPath = PATH_typo3conf .'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
		$this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';

		$this->fileGenerator->setSettings(
			array(
				'codeTemplateRootPath' => $this->codeTemplateRootPath,
				'extConf' => array(
					'enableRoundtrip'=>'1'
				)
			)
		);
		$this->fileGenerator->_set('codeTemplateRootPath', __DIR__ .'/../Resources/Private/CodeTemplates/Extbase/');
		$this->fileGenerator->_set('enableRoundtrip', true);
		$this->fileGenerator->_set('extension', $this->extension);
	}

	public function tearDown() {
		if (!empty($this->extension) && $this->extension->getExtensionKey() != NULL) {
			GeneralUtility::rmdir($this->extension->getExtensionDir(), TRUE);
		}
	}

	/**
	 * Helper function
	 * @param $name
	 * @param $entity
	 * @param $aggregateRoot
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
	 */
	protected function buildDomainObject($name, $entity = false, $aggregateRoot = false){
		$domainObject = $this->getMock(
			$this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject'),
			array('dummy')
		);
		/* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
		$domainObject->setExtension($this->extension);
		$domainObject->setName($name);
		$domainObject->setEntity($entity);
		$domainObject->setAggregateRoot($aggregateRoot);
		if ($aggregateRoot){
			$defaultActions = ['list','show','new','create','edit','update','delete'];
			foreach ($defaultActions as $actionName){
				$action = GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Action');
				$action->setName($actionName);
				if ($actionName == 'deleted'){
					$action->setNeedsTemplate = false;
				}
				$domainObject->addAction($action);
			}
		}
		return $domainObject;
	}

	/**
	 * Builds an initial class file to test parsing and modifiying of existing classes
	 *
	 * This class file is generated based on the CodeTemplates
	 * @param string $modelName
	 */
	protected function generateInitialModelClassFile($modelName){
		$domainObject = $this->buildDomainObject($modelName);
		$classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, FALSE);
		$modelClassDir = 'Classes/Domain/Model/';
		GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		GeneralUtility::writeFile($modelClassPath,$classFileContent);
	}

	protected function removeInitialModelClassFile($modelName){
		if (@file_exists($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php')){
			unlink($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php');
		}
		$this->assertFalse(file_exists($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php'), 'Dummy files could not be removed:' . $this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php');
	}

}