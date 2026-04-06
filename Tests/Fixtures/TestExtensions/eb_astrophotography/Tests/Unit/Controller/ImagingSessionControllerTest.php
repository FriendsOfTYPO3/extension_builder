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
class ImagingSessionControllerTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Controller\ImagingSessionController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcmeCorp\EbAstrophotography\Controller\ImagingSessionController::class))
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
    public function listActionFetchesAllImagingSessionsFromRepositoryAndAssignsThemToView(): void
    {
        $allImagingSessions = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imagingSessionRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\ImagingSessionRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $imagingSessionRepository->expects(self::once())->method('findAll')->will(self::returnValue($allImagingSessions));
        $this->subject->_set('imagingSessionRepository', $imagingSessionRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('imagingSessions', $allImagingSessions);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenImagingSessionToView(): void
    {
        $imagingSession = new \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('imagingSession', $imagingSession);

        $this->subject->showAction($imagingSession);
    }
}
