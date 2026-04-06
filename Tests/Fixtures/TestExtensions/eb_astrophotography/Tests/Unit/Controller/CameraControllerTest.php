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
class CameraControllerTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Controller\CameraController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcmeCorp\EbAstrophotography\Controller\CameraController::class))
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
    public function listActionFetchesAllCamerasFromRepositoryAndAssignsThemToView(): void
    {
        $allCameras = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cameraRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\CameraRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $cameraRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCameras));
        $this->subject->_set('cameraRepository', $cameraRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('cameras', $allCameras);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCameraToView(): void
    {
        $camera = new \AcmeCorp\EbAstrophotography\Domain\Model\Camera();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('camera', $camera);

        $this->subject->showAction($camera);
    }
}
