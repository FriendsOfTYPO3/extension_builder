<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\AstroFilterRepository;
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
 * AstroFilterController
 */
class AstroFilterController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\AstroFilterRepository $astroFilterRepository)
    {
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $astroFilters = $this->astroFilterRepository->findAll();
        $this->view->assign('astroFilters', $astroFilters);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilter
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilter): ResponseInterface
    {
        $this->view->assign('astroFilter', $astroFilter);
        return $this->htmlResponse();
    }
}
