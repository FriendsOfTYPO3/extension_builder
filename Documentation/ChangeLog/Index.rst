.. include:: /Includes.rst.txt

.. _changelog:

==========
Change log
==========

Version 13.3.0
--------------

* [FEATURE] Warn before deleting a model object that has connected relations — a confirmation dialog now lists affected relations and lets the user cancel or proceed.
* [FEATURE] Warn before discarding unsaved changes when switching extensions — a confirmation dialog prevents accidental data loss.
* [FEATURE] Introduce Storybook for isolated Lit component development.
* [FEATURE] Replace unmaintained ``Sho_Inflect`` with ``doctrine/inflector`` for pluralization and singularization.
* [BUGFIX] ``composer.json`` is now updated on each save and includes author email and company fields.
* [DOCS] Extend Roundtrip mode chapter with YAML config, split token, and operational guides.
* [DOCS] Replace ``Settings.cfg`` with ``guides.xml`` in generated documentation.
* [TASK] Improve GitHub workflows, issue templates and CI config.
* [TASK] Add commit message validation hook and tighten CI checks.
* [TASK] Add scheduled cleanup of old workflow runs.
* [TASK] Add regression test for newline after case label.

Version 13.2.0
--------------

* [FEATURE] TCA ``select`` properties can now have custom items configured directly in the domain modeling editor — each item has a label and a value.
* [TASK] ``locallang_csh_*.xlf`` files are no longer generated. CSH (Context Sensitive Help) was removed in TYPO3 v12; field descriptions are now written exclusively to ``locallang_db.xlf`` and referenced via the TCA ``description`` key. When a domain object is removed via RoundTrip, any leftover ``locallang_csh_*.xml`` and ``locallang_csh_*.xlf`` files are cleaned up automatically. Existing generated extensions may have orphaned CSH files that can be deleted manually.
* [BUGFIX] Renaming a domain object no longer corrupts the controller constructor and action parameter names.
* [BUGFIX] Property settings panel now correctly shows and hides fields based on the selected property type.
* [BUGFIX] Boolean properties no longer always appear as checked after loading an existing extension.
* [BUGFIX] Saving an extension no longer fails after a relation has been deleted.
* [BUGFIX] Opening old ``ExtensionBuilder.json`` files that are missing the ``renderType`` key no longer causes an error — the field is silently ignored.
* [BUGFIX] Indentation of nested method calls is preserved correctly during RoundTrip.
* [BUGFIX] Field descriptions are now preserved with their original casing in generated XLF files.
* [BUGFIX] ``SelectProperty`` type is now stored as a string instead of an integer, fixing issues when loading extensions that use select fields.
* [BUGFIX] Fixed undefined array key ``excludeField`` warning in ``ObjectSchemaBuilder``.

Version 13.1.0
--------------

* [BUGFIX] ``ext_tables.sql`` now includes a ``CREATE TABLE`` statement for models that have no own properties but are the target of a ``ZeroToMany inline`` relation — previously the FK column was silently dropped, leaving the database table uncreated.
* [BUGFIX] A validation warning is now shown when a domain object has no properties, informing the user that no ``CREATE TABLE`` statement will be generated in ``ext_tables.sql``.
* [BUGFIX] Generated controller actions now declare ``ResponseInterface`` as their return type, matching the TYPO3 v13 standard.
* [BUGFIX] ``f:image`` ViewHelper calls in generated templates now use the ``image`` attribute instead of ``src``, fixing rendering of filenames with Umlauts or special characters.
* [FEATURE] XLF files are no longer rewritten when only the ``date=`` attribute changed — avoids VCS noise on every regeneration. The ``staticDateInXliffFiles`` setting is removed as it is no longer needed.
* [FEATURE] Generated backend module extensions now include a ``user.tsconfig`` file that makes the backend module accessible without manual TSconfig setup.
* [FEATURE] ``extbase`` is now automatically added as a dependency to generated extensions that use Extbase controllers or domain objects.

Version 13.0.0
--------------

**Breaking changes and migrations (v12 → v13):**

* [TASK] Update dependencies to TYPO3 ^13.4, PHP ^8.3.
* [TASK] Frontend plugins are now registered as ``CType`` content elements using ``PLUGIN_TYPE_CONTENT_ELEMENT`` — the deprecated ``list_type`` approach is no longer used. ``page.tsconfig`` wizard entries are no longer generated because ``registerPlugin()`` with ``CType`` automatically adds the plugin to the content element wizard. Existing generated extensions using ``list_type`` must be regenerated to adopt the new registration.
* [TASK] TypoScript is no longer loaded via ``ext_typoscript_setup.typoscript`` (which was dropped in TYPO3 v13) — the extension now registers its TypoScript paths via ``addTypoScriptSetup()`` in ``ext_localconf.php``.
* [FEATURE] Extensions with frontend plugins can now optionally generate a **Site Set** (``Configuration/Sets/``). When the *Generate Site Set* option is enabled in the editor, the generator creates ``config.yaml``, ``setup.typoscript``, and ``constants.typoscript`` instead of the classic ``addStaticFile`` approach. The classic behavior is unchanged when the option is not enabled.
* [BUGFIX] Constructor property promotion flags (``readonly``, visibility modifiers) are now preserved correctly during RoundTrip code generation.
* [TASK] Generated TypoScript setup templates no longer include a ``storagePid`` setting — the line is commented out so integrators can enable it deliberately.

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
