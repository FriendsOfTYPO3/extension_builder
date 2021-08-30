<?php
defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
   'test_extension',
   'tx_testextension_domain_model_child3'
);
