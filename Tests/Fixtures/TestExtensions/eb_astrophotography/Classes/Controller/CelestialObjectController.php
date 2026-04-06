<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AcmeCorp\EbAstrophotography\Domain\Repository\CelestialObjectRepository;
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
 * CelestialObjectController
 */
class CelestialObjectController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(private readonly \AcmeCorp\EbAstrophotography\Domain\Repository\CelestialObjectRepository $celestialObjectRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $celestialObjects = $this->celestialObjectRepository->findAll();
        $this->view->assign('celestialObjects', $celestialObjects);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObject
     */
    public function showAction(\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObject)
    {
        $this->view->assign('celestialObject', $celestialObject);
        return $this->htmlResponse();
    }

    /**
     * action new
     */
    public function newAction()
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $newCelestialObject
     */
    public function createAction(\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $newCelestialObject)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->celestialObjectRepository->add($newCelestialObject);
        return $this->redirect('list');
    }
}
