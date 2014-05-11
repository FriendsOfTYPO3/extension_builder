<?php{namespace k=EBT\ExtensionBuilder\ViewHelpers}
namespace {extension.nameSpaceName}\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) <f:format.date format="Y">now</f:format.date> <f:for each="{extension.persons}" as="person">{person.name} <f:if condition="{person.email}"><{person.email}></f:if><f:if condition="{person.company}">, {person.company}</f:if>
 *  			</f:for>
 *  All rights reserved
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
 * Test case for class {domainObject.controllerClassName}.
 *
<f:for each="{extension.persons}" as="person"> * @author {person.name} <f:if condition="{person.email}"><{person.email}></f:if>
</f:for> */
class {controllerName}Test extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \{domainObject.controllerClassName}
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('{domainObject.controllerClassName -> k:format.escapeBackslashes()}', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

<f:for each="{domainObject.actions}" as="action"><f:if condition="{k:matchString(match:'list', in:action.name)}">

	/**
	 * @test
	 */
	public function listActionFetchesAll{domainObject.name -> k:pluralize()}FromRepositoryAndAssignsThemToView() {

		$all{domainObject.name -> k:pluralize()} = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMock('{domainObject.qualifiedDomainRepositoryClassName -> k:format.escapeBackslashes()}', array('findAll'), array(), '', FALSE);
		${domainObject.name -> k:format.lowercaseFirst()}Repository->expects($this->once())->method('findAll')->will($this->returnValue($all{domainObject.name -> k:pluralize()}));
		$this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('{domainObject.name -> k:pluralize() -> k:format.lowercaseFirst()}', $all{domainObject.name -> k:pluralize()});
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}</f:if><f:if condition="{k:matchString(match:'show', in:action.name)}">

	/**
	 * @test
	 */
	public function showActionAssignsTheGiven{domainObject.name}ToView() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

		$this->subject->showAction(${domainObject.name -> k:format.lowercaseFirst()});
	}</f:if><f:if condition="{k:matchString(match:'new', in:action.name)}">

	/**
	 * @test
	 */
	public function newActionAssignsTheGiven{domainObject.name}ToView() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('new{domainObject.name}', ${domainObject.name -> k:format.lowercaseFirst()});
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction(${domainObject.name -> k:format.lowercaseFirst()});
	}</f:if><f:if condition="{k:matchString(match:'create', in:action.name)}">

	/**
	 * @test
	 */
	public function createActionAddsTheGiven{domainObject.name}To{domainObject.name}Repository() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMock('{domainObject.qualifiedDomainRepositoryClassName -> k:format.escapeBackslashes()}', array('add'), array(), '', FALSE);
		${domainObject.name -> k:format.lowercaseFirst()}Repository->expects($this->once())->method('add')->with(${domainObject.name -> k:format.lowercaseFirst()});
		$this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

		$this->subject->createAction(${domainObject.name -> k:format.lowercaseFirst()});
	}</f:if><f:if condition="{k:matchString(match:'edit', in:action.name)}">

	/**
	 * @test
	 */
	public function editActionAssignsTheGiven{domainObject.name}ToView() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

		$this->subject->editAction(${domainObject.name -> k:format.lowercaseFirst()});
	}
</f:if><f:if condition="{k:matchString(match:'update', in:action.name)}">

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGiven{domainObject.name}In{domainObject.name}Repository() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMock('{domainObject.qualifiedDomainRepositoryClassName -> k:format.escapeBackslashes()}', array('update'), array(), '', FALSE);
		${domainObject.name -> k:format.lowercaseFirst()}Repository->expects($this->once())->method('update')->with(${domainObject.name -> k:format.lowercaseFirst()});
		$this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

		$this->subject->updateAction(${domainObject.name -> k:format.lowercaseFirst()});
	}</f:if><f:if condition="{k:matchString(match:'delete', in:action.name)}">

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGiven{domainObject.name}From{domainObject.name}Repository() {
		${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

		${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMock('{domainObject.qualifiedDomainRepositoryClassName -> k:format.escapeBackslashes()}', array('remove'), array(), '', FALSE);
		${domainObject.name -> k:format.lowercaseFirst()}Repository->expects($this->once())->method('remove')->with(${domainObject.name -> k:format.lowercaseFirst()});
		$this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

		$this->subject->deleteAction(${domainObject.name -> k:format.lowercaseFirst()});
	}</f:if></f:for><f:if condition="{domainObject.actions}"><f:then></f:then><f:else>
	/**
	 * @test
	 */
	public function dummyTestToNotLeaveThisFileEmpty() {
		$this->markTestIncomplete();
	}</f:else></f:if>
}
