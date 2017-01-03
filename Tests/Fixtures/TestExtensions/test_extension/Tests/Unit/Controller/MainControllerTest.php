<?php
namespace FIXTURE\TestExtension\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class MainControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \FIXTURE\TestExtension\Controller\MainController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\FIXTURE\TestExtension\Controller\MainController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllMainsFromRepositoryAndAssignsThemToView()
    {
        $allMains = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('findAll')->will(self::returnValue($allMains));
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('mains', $allMains);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenMainToView()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('main', $main);

        $this->subject->showAction($main);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenMainToMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('add')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->createAction($main);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenMainToView()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('main', $main);

        $this->subject->editAction($main);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenMainInMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('update')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->updateAction($main);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenMainFromMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMockBuilder(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $mainRepository->expects(self::once())->method('remove')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->deleteAction($main);
    }
}
