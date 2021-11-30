{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php

declare(strict_types=1);

namespace {extension.namespaceName}\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
<f:if condition="{extension.persons}"> *
<f:for each="{extension.persons}" as="person"> * @author {person.name} <f:if condition="{person.email}"><{person.email}></f:if>
</f:for></f:if> */
class {domainObject.name}Test extends UnitTestCase
{
    /**
     * @var {domainObject.fullQualifiedClassName}|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            {domainObject.fullQualifiedClassName}::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
<f:if condition="{f:count(subject:domainObject.properties)} > 0">
<f:then>
<f:for each="{domainObject.properties}" as="property">

    /**
     * @test
     */
    public function get{property.name -> k:format.uppercaseFirst()}ReturnsInitialValueFor<f:if condition="{k:matchString(match: 'FileReference', in:property.unqualifiedType)}"><f:then>FileReference</f:then><f:else>{f:if(condition: "{k:matchString(match: 'ObjectStorage', in: property.unqualifiedType)}", then: "{property.foreignModelName}", else: "{property.unqualifiedType -> k:format.uppercaseFirst()}")}</f:else></f:if>(): void
    {<f:if condition="{property.unqualifiedType} == 'int'">
        self::assertSame(
            0,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if><f:if condition="{property.unqualifiedType} == 'float'">
        self::assertSame(
            0.0,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if><f:if condition="{property.unqualifiedType} == 'string'">
        self::assertSame(
            '',
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if><f:if condition="{property.unqualifiedType} == 'bool'">
        self::assertFalse($this->subject->get{property.name -> k:format.uppercaseFirst()}());</f:if><f:if condition="{k:matchString(match: 'ObjectStorage', in:property.unqualifiedType)}"><f:then>
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:then><f:else><f:if condition="{property.foreignModel}">
        self::assertEquals(
            null,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if><f:if condition="{k:matchString(match:'DateTime', in:property.unqualifiedType)}">
        self::assertEquals(
            null,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if><f:if condition="{k:matchString(match:'FileReference', in:property.unqualifiedType)}">
        self::assertEquals(
            null,
            $this->subject->get{property.name -> k:format.uppercaseFirst()}()
        );</f:if></f:else></f:if>
    }

    /**
     * @test
     */
    public function set{property.name -> k:format.uppercaseFirst()}For<f:if condition="{k:matchString(match: 'FileReference', in:property.unqualifiedType)}"><f:then>FileReference</f:then><f:else>{f:if(condition:"{k:matchString(match: 'ObjectStorage', in: property.unqualifiedType)}", then: "ObjectStorageContaining{property.foreignModelName}", else: "{property.unqualifiedType -> k:format.uppercaseFirst()}")}</f:else></f:if>Sets{property.name -> k:format.uppercaseFirst()}(): void
    {<f:if condition="{property.unqualifiedType} == 'string'">
        $this->subject->set{property.name -> k:format.uppercaseFirst()}('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('{property.name}'));</f:if><f:if condition="{property.unqualifiedType}  == 'int'">
        $this->subject->set{property.name -> k:format.uppercaseFirst()}(12);

        self::assertEquals(12, $this->subject->_get('{property.name}'));</f:if><f:if condition="{property.unqualifiedType} == 'float'">
        $this->subject->set{property.name -> k:format.uppercaseFirst()}(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('{property.name}'));</f:if><f:if condition="{property.unqualifiedType} == 'bool'">
        $this->subject->set{property.name -> k:format.uppercaseFirst()}(true);

        self::assertEquals(true, $this->subject->_get('{property.name}'));</f:if><f:if condition="{k:matchString(match:'ObjectStorage', in:property.unqualifiedType)}"><f:then>
        ${property.name -> k:singularize()} = new {k:pregReplace(match:'/^.*<(.*)>$/', replace:'\1', subject:property.unqualifiedType)}();
        $objectStorageHoldingExactlyOne{property.name -> k:format.uppercaseFirst()} = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOne{property.name -> k:format.uppercaseFirst()}->attach(${property.name -> k:singularize()});
        $this->subject->set{property.name -> k:format.uppercaseFirst()}($objectStorageHoldingExactlyOne{property.name -> k:format.uppercaseFirst()});

        self::assertEquals($objectStorageHoldingExactlyOne{property.name -> k:format.uppercaseFirst()}, $this->subject->_get('{property.name}'));</f:then><f:else><f:if condition="{property.foreignModel}">
        ${property.name}Fixture = new {f:if(condition:"{k:matchString(match:'ObjectStorage', in:property.unqualifiedType)}", then:"ObjectStorageContaining{property.foreignModelName)}", else:"{property.foreignModel.fullQualifiedClassName}")}();
        $this->subject->set{property.name -> k:format.uppercaseFirst()}(${property.name}Fixture);

        self::assertEquals(${property.name}Fixture, $this->subject->_get('{property.name}'));</f:if><f:if condition="{k:matchString(match:'DateTime', in:property.unqualifiedType)}">
        $dateTimeFixture = new \DateTime();
        $this->subject->set{property.name -> k:format.uppercaseFirst()}($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('{property.name}'));</f:if><f:if condition="{k:matchString(match:'FileReference', in:property.unqualifiedType)}">
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->set{property.name -> k:format.uppercaseFirst()}($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('{property.name}'));</f:if></f:else></f:if>
    }<f:if condition="{k:matchString(match:'ObjectStorage', in:property.unqualifiedType)}">

    /**
     * @test
     */
    public function add{property.name -> k:singularize() -> k:format.uppercaseFirst()}ToObjectStorageHolding{property.name -> k:format.uppercaseFirst()}(): void
    {
        ${property.name -> k:singularize()} = new {property.foreignClassName}();
        ${property.name}ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        ${property.name}ObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo(${property.name -> k:singularize()}));
        $this->subject->_set('{property.name}', ${property.name}ObjectStorageMock);

        $this->subject->add{property.name -> k:singularize() -> k:format.uppercaseFirst()}(${property.name -> k:singularize()});
    }

    /**
     * @test
     */
    public function remove{property.name -> k:singularize() -> k:format.uppercaseFirst()}FromObjectStorageHolding{property.name -> k:format.uppercaseFirst()}(): void
    {
        ${property.name -> k:singularize()} = new {property.foreignClassName}();
        ${property.name}ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        ${property.name}ObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo(${property.name -> k:singularize()}));
        $this->subject->_set('{property.name}', ${property.name}ObjectStorageMock);

        $this->subject->remove{property.name -> k:singularize() -> k:format.uppercaseFirst()}(${property.name -> k:singularize()});
    }</f:if></f:for></f:then><f:else>
    /**
     * @test
     */
    public function dummyTestToNotLeaveThisFileEmpty(): void
    {
        self::markTestIncomplete();
    }</f:else></f:if>
}
