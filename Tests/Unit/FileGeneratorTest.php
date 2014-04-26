<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
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


/**
 *
 * @author Nico de Haen
 *
 */
class FileGeneratorUnitTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * Generate the appropriate code for a simple model class
	 * for a non aggregate root domain object with one boolean property
	 *
	 * @test
	 */
	function generateCodeForModelClassWithBooleanProperty() {
		$modelName = 'ModelCgt1';
		$propertyName = 'blue';
		$domainObject = $this->buildDomainObject($modelName);
		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty();
		$property->setName($propertyName);
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, FALSE);
		$this->assertRegExp("/.*class ModelCgt1.*/", $classFileContent, 'Class declaration was not generated');
		$this->assertRegExp('/.*protected \\$blue.*/', $classFileContent, 'protected boolean property was not generated');
		$this->assertRegExp('/.*\* \@var boolean.*/', $classFileContent, 'var tag for boolean property was not generated');
		$this->assertRegExp('/.*\* \@validate NotEmpty.*/', $classFileContent, 'validate tag for required property was not generated');
		$this->assertRegExp('/.*public function getBlue\(\).*/', $classFileContent, 'Getter for boolean property was not generated');
		$this->assertRegExp('/.*public function setBlue\(\$blue\).*/', $classFileContent, 'Setter for boolean property was not generated');
		$this->assertRegExp('/.*public function isBlue\(\).*/', $classFileContent, 'is method for boolean property was not generated');
	}

}
