<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'EbAstrophotography',
        'Imagegallery',
        [
            \AcmeCorp\EbAstrophotography\Controller\AstroImageController::class => 'list, show'
        ],
        // non-cacheable actions
        [
            \AcmeCorp\EbAstrophotography\Controller\AstroImageController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\CelestialObjectController::class => 'create',
            \AcmeCorp\EbAstrophotography\Controller\TelescopeController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\CameraController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\AstroFilterController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ObservingSiteController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ImagingSessionController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ProcessingRecipeController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\AwardController::class => ''
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'EbAstrophotography',
        'Skyatlas',
        [
            \AcmeCorp\EbAstrophotography\Controller\CelestialObjectController::class => 'list, show'
        ],
        // non-cacheable actions
        [
            \AcmeCorp\EbAstrophotography\Controller\AstroImageController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\CelestialObjectController::class => 'create',
            \AcmeCorp\EbAstrophotography\Controller\TelescopeController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\CameraController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\AstroFilterController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ObservingSiteController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ImagingSessionController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\ProcessingRecipeController::class => '',
            \AcmeCorp\EbAstrophotography\Controller\AwardController::class => ''
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

})();
