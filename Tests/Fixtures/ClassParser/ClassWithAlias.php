<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Tx_ExtensionBuilder_Tests_Examples_ClassParser_ClassWithAlias
{

    /**
     *
     * @return array $names
     */
    public function devLog()
    {
        GeneralUtility::devLog('Test', 'test', 1);
    }
}
