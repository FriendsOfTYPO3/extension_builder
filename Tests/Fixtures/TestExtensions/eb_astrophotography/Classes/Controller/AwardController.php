<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\AwardRepository;
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
 * AwardController
 */
class AwardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\AwardRepository $awardRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $awards = $this->awardRepository->findAll();
        $this->view->assign('awards', $awards);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Award $award
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\Award $award)
    {
        $this->view->assign('award', $award);
        return $this->htmlResponse();
    }
}
