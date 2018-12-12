{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
namespace {extension.nameSpaceName}\Tests\Unit\Controller;

/**
 * Test case.
<f:if condition="{extension.persons}"> *
<f:for each="{extension.persons}" as="person"> * @author {person.name} <f:if condition="{person.email}"><{person.email}></f:if>
</f:for></f:if> */
class {controllerName}Test extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \{domainObject.controllerClassName}
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\{domainObject.controllerClassName}::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

<f:for each="{domainObject.actions}" as="action"><f:if condition="{action.name} == 'list'">

    /**
     * @test
     */
    public function listActionFetchesAll{domainObject.name -> k:pluralize()}FromRepositoryAndAssignsThemToView()
    {

        $all{domainObject.name -> k:pluralize()} = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('findAll')->will(self::returnValue($all{domainObject.name -> k:pluralize()}));
        $this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:pluralize() -> k:format.lowercaseFirst()}', $all{domainObject.name -> k:pluralize()});
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }</f:if><f:if condition="{k:matchString(match:'show', in:action.name)}">

    /**
     * @test
     */
    public function showActionAssignsTheGiven{domainObject.name}ToView()
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

        $this->subject->showAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'create', in:action.name)}">

    /**
     * @test
     */
    public function createActionAddsTheGiven{domainObject.name}To{domainObject.name}Repository()
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('add')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->createAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'edit', in:action.name)}">

    /**
     * @test
     */
    public function editActionAssignsTheGiven{domainObject.name}ToView()
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});

        $this->subject->editAction(${domainObject.name -> k:format.lowercaseFirst()});
    }
</f:if><f:if condition="{k:matchString(match:'update', in:action.name)}">

    /**
     * @test
     */
    public function updateActionUpdatesTheGiven{domainObject.name}In{domainObject.name}Repository()
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('update')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->updateAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if><f:if condition="{k:matchString(match:'delete', in:action.name)}">

    /**
     * @test
     */
    public function deleteActionRemovesTheGiven{domainObject.name}From{domainObject.name}Repository()
    {
        ${domainObject.name -> k:format.lowercaseFirst()} = new {domainObject.fullQualifiedClassName}();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository = $this->getMockBuilder(\{domainObject.qualifiedDomainRepositoryClassName}::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        ${domainObject.name -> k:format.lowercaseFirst()}Repository->expects(self::once())->method('remove')->with(${domainObject.name -> k:format.lowercaseFirst()});
        $this->inject($this->subject, '{domainObject.name -> k:format.lowercaseFirst()}Repository', ${domainObject.name -> k:format.lowercaseFirst()}Repository);

        $this->subject->deleteAction(${domainObject.name -> k:format.lowercaseFirst()});
    }</f:if></f:for><f:if condition="{domainObject.actions}"><f:then></f:then><f:else>
    /**
     * @test
     */
    public function dummyTestToNotLeaveThisFileEmpty()
    {
        self::markTestIncomplete();
    }</f:else></f:if>
}
