<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author John Doe <mail@typo3.com>
 */
class MainControllerTest extends UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Controller\MainController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\FIXTURE\TestExtension\Controller\MainController::class))
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
    public function listActionFetchesAllMainsFromRepositoryAndAssignsThemToView(): void
    {
        $allMains = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $mainRepository->expects(self::once())->method('findAll')->will(self::returnValue($allMains));
        $this->subject->_set('mainRepository', $mainRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('mains', $allMains);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenMainToView(): void
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('main', $main);

        $this->subject->showAction($main);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenMainToMainRepository(): void
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('add')->with($main);
        $this->subject->_set('mainRepository', $mainRepository);

        $this->subject->createAction($main);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenMainToView(): void
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('main', $main);

        $this->subject->editAction($main);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenMainInMainRepository(): void
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('update')->with($main);
        $this->subject->_set('mainRepository', $mainRepository);

        $this->subject->updateAction($main);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenMainFromMainRepository(): void
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('remove')->with($main);
        $this->subject->_set('mainRepository', $mainRepository);

        $this->subject->deleteAction($main);
    }
}
