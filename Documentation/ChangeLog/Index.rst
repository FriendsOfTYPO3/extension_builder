.. include:: /Includes.rst.txt
.. _changelog:

==========
Change log
==========

Important release notes
=======================

.. toctree::
   :maxdepth: 1
   :titlesonly:
   :glob:

   12-0-0

Version 12.0.0
--------------
* [TASK] Rework the whole JS GUI of this extension
* [TASK] Raise new versions for new TYPO3 12 release
* [TASK] Drop support for TYPO3 11, add support for TYPO3 12
* [TASK] Drop support for PHP 7.x, add support for PHP 8.1 and 8.2
* [TASK] Register each plugin as its own CType
* [DOCS] Update the documentation
* [TASK] Removed generation of Tests for the extensions

Version 11.0.13
---------------
* [DOCS] Adds information about a possible missing storage path when using composer mode

Version 11.0.12
---------------
* [TASK] Switch documentation rendering to PHP (thanks to Sandra Erbel)
* [BUGFIX] fix issue with default value for nodefactory
* [BUGFIX] Enables scroll view of extension save dialog confirmations (thanks to warki)

Version 11.0.11
---------------
* [TASK] Use current standard for web-dir (thanks to Sybille Peters)
* [BUGFIX] - Undefined array key $parentClass

Version 11.0.10
---------------
* [BUGFIX] Allow null for native date and time
* [TASK] one controller action pair per line
* [FEATURE] add tca field description
* [BUGFIX] resolve nullable types correctly

Version 11.0.9
--------------
* [BUGFIX] Generate correct TCA for images and files
* [TASK] Corrected CSS-Default-Styles
* [DOCS] Small changes in Documentation

Version 11.0.8
-------------
* [BUGFIX] fixes links in extension module
* [TASK] Set the description field of backend module to textarea
* [BUGFIX] Fix issue in JS - "Relations"

Version 11.0.7
--------------
* Fix release number inside ext_emconf.php

Version 11.0.6
--------------
* [BUGFIX] Revert deletion inside composer.json

Version 11.0.5
--------------
* [BUGFIX] revert deletion of code templates

Version 11.0.4
--------------
* [BUGFIX] Fix warning if setDefaultValuesForClassProperties does not exist
* [DOCS] Add sponsoring page
* [BUGFIX] fixes title for advanced option button
* [BUGFIX] Generate correct .xlf files
* [BUGFIX] Language file not merged
* [BUGFIX] issue 599 missing property settings

Version 11.0.3
--------------
* [TASK] Add support for typo3/cms-composer-installers v4

Version 11.0.2
--------------
* [DOCS] Checkbox was renamed to "Generate documentation"
* [DOCS] Fix controller action names in blog example
* [DOCS] Small fixes derived from backport of documentation
* [DOCS] Small fixes derived from backport of documentation v11
* [TASK] Make Extension Builder compatible with PHP 8.0
* [TASK] Add allowed composer plugins
* [TASK] Update return type hint for model getters
* [BUGFIX] Strip trailing spaces after comma
* [DOCS] Rename slack channel to #extension-builder
* [TASK] Align with new TYPO3 documentation standards
* [TASK] Align with new TYPO3 documentation standards (follow-up)
* [BUGFIX] Fix PHP8 warning because overwriteSettings not found in empty settings

Version 11.0.1
--------------
* Small bugfixes
* Several improvements inside documentation
* [TASK] Switch extension stability from "beta" to "stable"
* [TASK] Remove suffix from generated folder Documentation.tmpl
* [TASK] Remove plugin type
* [TASK] Adapt public resources url for acceptance tests
