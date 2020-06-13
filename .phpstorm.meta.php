<?php
/** @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html */

namespace PHPSTORM_META {
    override(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(0), type(0));

    // TYPO3 Core testing classes
    // The accesible mock will be of type "self" as well as "MockObject" and "AccessibleObjectInterface"
    override(
        \TYPO3\CMS\Core\Tests\BaseTestCase::getAccessibleMock(0),
        map([
            '' => '@|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface',
        ])
    );
    override(
        \TYPO3\CMS\Core\Tests\BaseTestCase::getAccessibleMockForAbstractClass(0),
        map([
            '' => '@|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface',
        ])
    );
}
