<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\TelescopeRepository;
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
 * TelescopeController
 */
class TelescopeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\TelescopeRepository $telescopeRepository)
    {
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $telescopes = $this->telescopeRepository->findAll();
        $this->view->assign('telescopes', $telescopes);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescope
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescope): ResponseInterface
    {
        $this->view->assign('telescope', $telescope);
        return $this->htmlResponse();
    }
}
