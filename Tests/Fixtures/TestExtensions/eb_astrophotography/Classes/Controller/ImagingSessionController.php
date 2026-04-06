<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\ImagingSessionRepository;
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
 * ImagingSessionController
 */
class ImagingSessionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\ImagingSessionRepository $imagingSessionRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $imagingSessions = $this->imagingSessionRepository->findAll();
        $this->view->assign('imagingSessions', $imagingSessions);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSession
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSession)
    {
        $this->view->assign('imagingSession', $imagingSession);
        return $this->htmlResponse();
    }
}
