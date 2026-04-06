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
class ProcessingRecipeControllerTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Controller\ProcessingRecipeController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcmeCorp\EbAstrophotography\Controller\ProcessingRecipeController::class))
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
    public function listActionFetchesAllProcessingRecipesFromRepositoryAndAssignsThemToView(): void
    {
        $allProcessingRecipes = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $processingRecipeRepository = $this->getMockBuilder(\AcmeCorp\EbAstrophotography\Domain\Repository\ProcessingRecipeRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $processingRecipeRepository->expects(self::once())->method('findAll')->will(self::returnValue($allProcessingRecipes));
        $this->subject->_set('processingRecipeRepository', $processingRecipeRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('processingRecipes', $allProcessingRecipes);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenProcessingRecipeToView(): void
    {
        $processingRecipe = new \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('processingRecipe', $processingRecipe);

        $this->subject->showAction($processingRecipe);
    }
}
