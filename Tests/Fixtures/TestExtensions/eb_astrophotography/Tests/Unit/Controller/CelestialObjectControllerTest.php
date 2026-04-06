<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 */
class CelestialObjectControllerTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Controller\CelestialObjectController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcmeCorp\EbAstrophotography\Controller\CelestialObjectController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllCelestialObjectsFromRepositoryAndAssignsThemToView(): void
    {
        $allCelestialObjects = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $celestialObjectRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\CelestialObjectRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $celestialObjectRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCelestialObjects));
        $this->subject->_set('celestialObjectRepository', $celestialObjectRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('celestialObjects', $allCelestialObjects);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCelestialObjectToView(): void
    {
        $celestialObject = new \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('celestialObject', $celestialObject);

        $this->subject->showAction($celestialObject);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCelestialObjectToCelestialObjectRepository(): void
    {
        $celestialObject = new \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject();

        $celestialObjectRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\CelestialObjectRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $celestialObjectRepository->expects(self::once())->method('add')->with($celestialObject);
        $this->subject->_set('celestialObjectRepository', $celestialObjectRepository);

        $this->subject->createAction($celestialObject);
    }
}
