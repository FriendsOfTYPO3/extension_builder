{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php

declare(strict_types=1);

namespace {extension.namespaceName}\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
<f:if condition="{extension.persons}"> *
<f:for each="{extension.persons}" as="person"> * @author {person.name} <f:if condition="{person.email}"><{person.email}></f:if>
</f:for></f:if> */
class {controllerName}Test extends UnitTestCase
{
    /**
     * @var \{domainObject.controllerClassName}|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\{domainObject.controllerClassName}::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

<f:for each="{domainObject.actions}" as="action"><f:if condition="{action.name} == 'list'">

    /**
     * @test
     */
    public function listActionFetchesAll{domainObject.name -> k:pluralize()}FromRepositoryAndAssignsThemToView(): void
    {
        $all{domainObject.name -> k:pluralize()} = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('findAll')->will(self::returnValue($all{domainObject.name -> k:pluralize()}));
        $this->subject->_set('{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:pluralize() -> k:format.lowercaseFirst()}', $all{domainObject.name -> k:pluralize()});
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }</f:if><f:if condition="{k:matchString(match:'show', in:action.name)}">

    /**
     * @test
     */
    public function showActionAssignsTheGiven{domainObject.name}ToView(): void
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

        $this->subject->showAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'create', in:action.name)}">

    /**
     * @test
     */
    public function createActionAddsTheGiven{domainObject.name}To{domainObject.name}Repository(): void
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('add')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->subject->_set('{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->createAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'edit', in:action.name)}">

    /**
     * @test
     */
    public function editActionAssignsTheGiven{domainObject.name}ToView(): void
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

        $this->subject->editAction(${domainObject.name -> k:format.lowercaseFirst()});
    }
</f:if><f:if condition="{k:matchString(match:'update', in:action.name)}">

    /**
     * @test
     */
    public function updateActionUpdatesTheGiven{domainObject.name}In{domainObject.name}Repository(): void
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('update')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->subject->_set('{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->updateAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'delete', in:action.name)}">

    /**
     * @test
     */
    public function deleteActionRemovesTheGiven{domainObject.name}From{domainObject.name}Repository(): void
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('remove')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->subject->_set('{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->deleteAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if></f:for><f:if condition="{domainObject.actions}"><f:then></f:then><f:else>
    /**
     * @test
     */
    public function dummyTestToNotLeaveThisFileEmpty(): void
    {
        self::markTestIncomplete();
    }</f:else></f:if>
}
