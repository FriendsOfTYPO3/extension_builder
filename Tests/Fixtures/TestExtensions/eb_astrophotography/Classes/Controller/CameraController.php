<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\CameraRepository;
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
 * CameraController
 */
class CameraController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\CameraRepository $cameraRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $cameras = $this->cameraRepository->findAll();
        $this->view->assign('cameras', $cameras);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Camera $camera
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\Camera $camera)
    {
        $this->view->assign('camera', $camera);
        return $this->htmlResponse();
    }
}
