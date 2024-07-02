<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Controller;

use FIXTURE\TestExtension\Controller\MainController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use FIXTURE\TestExtension\Domain\Repository\MainRepository;
use FIXTURE\TestExtension\Domain\Model\Main;
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
     * @var MainController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(MainController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
    }

    /**
     * @test
     */
    public function listActionFetchesAllMainsFromRepositoryAndAssignsThemToView(): void
    {
        $allMains = $this->getMockBuilder(ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository = $this->getMockBuilder(MainRepository::class)
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
        $main = new Main();

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
        $main = new Main();

        $mainRepository = $this->getMockBuilder(MainRepository::class)
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
        $main = new Main();

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
        $main = new Main();

        $mainRepository = $this->getMockBuilder(MainRepository::class)
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
        $main = new Main();

        $mainRepository = $this->getMockBuilder(MainRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('remove')->with($main);
        $this->subject->_set('mainRepository', $mainRepository);

        $this->subject->deleteAction($main);
    }
}
