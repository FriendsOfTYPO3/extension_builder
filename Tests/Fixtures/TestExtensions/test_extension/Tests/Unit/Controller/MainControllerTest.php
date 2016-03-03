<?php
namespace FIXTURE\TestExtension\Tests\Unit\Controller;

/**
 * Test case for class FIXTURE\TestExtension\Controller\MainController.
 *
 * @author John Doe <mail@typo3.com>
 */
class MainControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \FIXTURE\TestExtension\Controller\MainController
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = $this->getMock(\FIXTURE\TestExtension\Controller\MainController::class, ['redirect', 'forward', 'addFlashMessage'], [], '', false);
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function listActionFetchesAllMainsFromRepositoryAndAssignsThemToView()
    {
        $allMains = $this->getMock(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class, [], [], '', false);

        $mainRepository = $this->getMock(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class, ['findAll'], [], '', false);
        $mainRepository->expects($this->once())->method('findAll')->will($this->returnValue($allMains));
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects($this->once())->method('assign')->with('mains', $allMains);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenMainToView()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('main', $main);

        $this->subject->showAction($main);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenMainToMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMock(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class, ['add'], [], '', false);
        $mainRepository->expects($this->once())->method('add')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->createAction($main);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenMainToView()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('main', $main);

        $this->subject->editAction($main);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenMainInMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMock(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class, ['update'], [], '', false);
        $mainRepository->expects($this->once())->method('update')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->updateAction($main);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenMainFromMainRepository()
    {
        $main = new \FIXTURE\TestExtension\Domain\Model\Main();

        $mainRepository = $this->getMock(\FIXTURE\TestExtension\Domain\Repository\MainRepository::class, ['remove'], [], '', false);
        $mainRepository->expects($this->once())->method('remove')->with($main);
        $this->inject($this->subject, 'mainRepository', $mainRepository);

        $this->subject->deleteAction($main);
    }
}
