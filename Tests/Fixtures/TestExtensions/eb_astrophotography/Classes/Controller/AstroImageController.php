<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\AstroImageRepository;
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
 * AstroImageController
 */
class AstroImageController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\AstroImageRepository $astroImageRepository)
    {
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $astroImages = $this->astroImageRepository->findAll();
        $this->view->assign('astroImages', $astroImages);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\AstroImage $astroImage
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\AstroImage $astroImage): ResponseInterface
    {
        $this->view->assign('astroImage', $astroImage);
        return $this->htmlResponse();
    }
}
