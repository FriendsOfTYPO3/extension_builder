<?php

declare(strict_types=1);

/*
 * This file is part of the "extension_builder" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

require __DIR__ . '/../.Build/vendor/autoload.php';

// Needed for some tests
defined('LF') ?: define('LF', chr(10));
defined('CR') ?: define('CR', chr(13));
defined('CRLF') ?: define('CRLF', CR . LF);
