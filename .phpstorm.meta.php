<?php
/** @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html */

namespace PHPSTORM_META {
    override(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(0), type(0));

    // The accesible mock will be of type "self" as well as "MockObject" and "AccessibleObjectInterface"
    override(
        \TYPO3\TestingFramework\Core\BaseTestCase::getAccessibleMock(0),
        map([
            '' =>  '@|\PHPUnit\Framework\MockObject\MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface',
        ])
    );
}
