.. include:: /Includes.rst.txt

.. _changelog:

==========
Change log
==========

Version 13.1.0
--------------

* [BUGFIX] ``ext_tables.sql`` now includes a ``CREATE TABLE`` statement for models that have no own properties but are the target of a ``ZeroToMany inline`` relation — previously the FK column was silently dropped, leaving the database table uncreated.
* [BUGFIX] A validation warning is now shown when a domain object has no properties, informing the user that no ``CREATE TABLE`` statement will be generated in ``ext_tables.sql``.
* [FEATURE] XLF files are no longer rewritten when only the ``date=`` attribute changed — avoids VCS noise on every regeneration. The ``staticDateInXliffFiles`` setting is removed as it is no longer needed.

Version 12.0.0
--------------

**Breaking changes and migrations (v11 → v12):**

* [TASK] Update dependencies to TYPO3 ^12.4, PHP ^8.3, PHPUnit ^10, testing-framework ^7, add Rector
* [TASK] Migrate backend module registration from ``ext_tables.php`` to ``Configuration/Backend/Modules.php``
* [TASK] Rename TypoScript setup file extension from ``.txt`` to ``.typoscript``
* [TASK] Replace ``GeneralUtility::makeInstance()`` with Dependency Injection throughout
* [TASK] Migrate setter injection to constructor injection in all controller and service classes
* [TASK] Replace YUI/WireIt/InputEx with Lit Web Components and TYPO3 v12 CSS variables
* [TASK] Replace yarn/SCSS build pipeline with Vite and ESM module bundling (npm)
* [TASK] Add Playwright E2E test infrastructure
* [TASK] Migrate TCA: ``type=number`` (was ``type=input``/``eval=int``), ``type=link`` (was ``renderType=inputLink``)
* [TASK] Migrate TCA items arrays to associative format (``label``/``value`` keys)

Version 11.0.13
---------------
* [DOCS] Adds information about a possible missing storage path when using composer mode
* Bugfixes

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
