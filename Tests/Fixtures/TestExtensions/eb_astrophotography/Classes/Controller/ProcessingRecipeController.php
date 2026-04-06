<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\ProcessingRecipeRepository;
use Psr\Http\Message\ResponseInterface;

/**
 * This file is part of the "EB Astrophotography" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2026 
 */

/**
 * ProcessingRecipeController
 */
class ProcessingRecipeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\ProcessingRecipeRepository $processingRecipeRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $processingRecipes = $this->processingRecipeRepository->findAll();
        $this->view->assign('processingRecipes', $processingRecipes);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipe
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipe)
    {
        $this->view->assign('processingRecipe', $processingRecipe);
        return $this->htmlResponse();
    }
}
