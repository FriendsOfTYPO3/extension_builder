<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager as Config;

class Tx_ExtensionBuilder_Tests_Examples_ClassParser_ClassWithAlias {


	/**
	 *
	 * @return array $names
	 */
	public function devlog(){
		GeneralUtility::devlog('Test','test',1);
	}

}
