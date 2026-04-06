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
class TelescopeControllerTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Controller\TelescopeController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcmeCorp\EbAstrophotography\Controller\TelescopeController::class))
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
    public function listActionFetchesAllTelescopesFromRepositoryAndAssignsThemToView(): void
    {
        $allTelescopes = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $telescopeRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\TelescopeRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $telescopeRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTelescopes));
        $this->subject->_set('telescopeRepository', $telescopeRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('telescopes', $allTelescopes);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTelescopeToView(): void
    {
        $telescope = new \AcmeCorp\EbAstrophotography\Domain\Model\Telescope();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('telescope', $telescope);

        $this->subject->showAction($telescope);
    }
}
