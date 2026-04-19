.. include:: /Includes.rst.txt

==============
Roundtrip mode
==============

Sooner or later you get to the point where you need to add or rename domain
objects, but already changed code of the originally created files. At this
point the *roundtrip mode* is needed.
It aims to preserve your manual changes while applying the new domain model
configuration:

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 1
   :local:

.. _editing-existing-extension:

Editing an existing extension
=============================

The roundtrip mode is enabled by default. To disable it, see the Extension
Builder configuration :doc:`/Configuration/Index`.

The general rule is: All configurations that can be edited in the graphical
editor should be applied in the graphical editor. For example, if your
extension depends on another extension, you should add it in the properties form
of the graphical editor and not directly in the :file:`ext_emconf.php` and
:file:`composer.json`.

Make sure you configure the :ref:`overwrite-settings`.

.. _yaml-configuration-for-roundtrip:

YAML configuration
==================

The file :file:`Configuration/ExtensionBuilder/settings.yaml` in your
extension controls how each file or folder is handled when the extension is
saved again in the Extension Builder. The top-level key is
``overwriteSettings``; its nesting mirrors the file system structure of the
extension.

Three values are available:

``merge``
   **Class files:** All properties, methods and method bodies are updated
   individually — your custom code inside methods is preserved.

   **Language files:** Existing keys and their translations are kept;
   new keys are added.

   **All other files:** A :ref:`split token <split-token>` is placed at the
   end of the generated section. Everything *before* the token is overwritten
   on each save; everything *after* the token is preserved.

``keep``
   The file or folder is never overwritten after its initial creation.

   .. warning::

      Using ``keep`` on files that the Extension Builder must update (e.g.
      TCA or SQL) may break the ability to edit the extension in the
      graphical editor.

``skip``
   The file or folder is never created by the Extension Builder.

   .. warning::

      Same risk as ``keep`` — use with care.

Example :file:`Configuration/ExtensionBuilder/settings.yaml`:

.. code-block:: yaml

   overwriteSettings:
     Classes:
       Controller: merge
       Domain:
         Model: merge
         Repository: merge

     Configuration:
       #TCA: merge
       #TypoScript: keep

     Resources:
       Private:
         Language: merge
         #Templates: keep

     Documentation: skip

.. _split-token:

Split token
===========

When a file's overwrite setting is ``merge``, the Extension Builder inserts a
special marker line — the *split token* — at the end of the auto-generated
section:

.. code-block:: none

   ## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

Everything **before** this token is regenerated on every save and must not be
edited manually. Everything **after** this token is preserved across saves and
is the right place for your custom additions.

The token is used in non-PHP, non-language files such as TypoScript setup or
YAML configuration files. PHP class files and language files use a more
granular merge strategy instead (see the ``merge`` description above).

**Example** — adding a custom TypoScript constant after the token:

.. code-block:: typoscript

   plugin.tx_myextension {
       view.templateRootPaths.0 = EXT:my_extension/Resources/Private/Templates/
       persistence.storagePid = 42
   }
   ## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

   # Custom override: use a different storage page on staging
   plugin.tx_myextension.persistence.storagePid = 99

.. _overview-of-the-roundtrip-features:

Overview of the roundtrip features
==================================

Consequences of various actions:

**If you change the extension key:**

*  the extension will be copied to a folder named like the new extension key

*  all classes and tables are renamed

*  your code should be adapted to the new names (not just overridden with the
   default code)

**If you change the vendor name:**

*  namespaces in all class files are updated

*  use/import statements referencing the old vendor namespace are updated

*  type hints, var types, and return types in methods and parameters are updated

*  TypoScript files are regenerated with the new vendor name automatically

**If you rename a property:**

*  the corresponding class property is renamed

*  the corresponding getter and setter methods are updated

*  TCA files and SQL definitions are newly generated, modifications will be LOST

*  existing data in the corresponding table field will be LOST, except you
   RENAME the field in the database manually

**If you rename a relation property:**

*  the corresponding class property is renamed

*  the corresponding getter/setter or add/remove methods are updated

*  the new SQL definition and default TCA configuration will be generated

*  existing data in the corresponding table field will be LOST, except you
   RENAME the field in the database manually

*  existing data in many-to-many database tables will be LOST, except you RENAME
   the table manually

**If you change a property type:**

*  the var type doc comment tags are updated

*  the type hint for the parameter in getter and setter methods are updated

*  the SQL type is updated if necessary

*  existing data in the corresponding table field might get LOST, except you
   ALTER the field type in the database manually

**If you change a relation type:**

*  if you switch the type from 1:1 to 1:n or n:m or vice versa the getter/setter
   or add/remove methods will be lost (!)

*  the required new getter/setter/ or add/remove methods are generated with the
   default method body

*  existing data in many-to-many database tables will be LOST, except you RENAME
   the table manually

**If you rename a domain object:**

*  the corresponding classes (domain object, controller, repository) will be
   renamed

*  all methods, properties and constants are preserved

*  relations to this domain object are updated

*  references to the renamed classes in OTHER classes are NOT updated (!)

*  TCA files and SQL definitions are new generated, modifications will be LOST

**If you delete a domain object:**

*  the model class (:file:`Classes/Domain/Model/{Name}.php`) is deleted

*  the TCA file (:file:`Configuration/TCA/{Name}.php`) is deleted

*  if the domain object is an aggregate root: the controller and repository
   class files are deleted

*  language files for context-sensitive help (``locallang_csh_*``) are deleted

*  the database table and any existing data are **NOT** removed automatically;
   you need to drop the table manually if it is no longer needed

**If you delete a property:**

*  the class property is removed from the domain model

*  the corresponding getter and setter methods are removed

*  for relation properties: the ``add*()`` and ``remove*()`` methods are removed
   instead

*  for boolean properties: the ``is*()`` method is removed

*  TCA and SQL definitions are regenerated, any manual modifications will be LOST

*  the database column and any existing data are **NOT** removed automatically

.. _change-preview:

Change preview
==============

When you save an existing extension, the Extension Builder detects whether any
structural changes would affect files on disk. If changes are detected, a
confirmation dialog is shown **before** any files are written:

*  **Files that will be modified** — lists each affected file, together with
   which methods will be added, renamed or removed

*  **Files that will be deleted** — lists files that will be permanently removed
   from disk (e.g. after deleting a domain object)

The dialog has two buttons:

*  **Cancel** — aborts the save; no files are changed

*  **Generate** — confirms and writes all changes

If no structural changes are detected the dialog is skipped and the extension
is saved immediately.

.. _backup-and-restore:

Backup and restore
==================

The Extension Builder can create a full backup of an extension every time it
is saved. This is enabled by default and can be configured under
:ref:`global-configuration`.

**Creating a backup**

A backup is created automatically on each save when the *Backup on save* option
is active. The backup is stored as a timestamped copy of the entire extension
folder in the configured backup directory
(default: :file:`var/tx_extensionbuilder/backups`).

**Restoring a backup**

Click the :guilabel:`Restore backup` button in the Extension Builder toolbar
(top-left area, next to the Save button). A modal opens with a list of all
available backups for the currently loaded extension. Each entry shows a
timestamp label and the number of files in that backup snapshot.

Select the desired backup from the list and click :guilabel:`Restore`. The
entire extension folder is replaced with the contents of the selected backup.

.. warning::

   Restoring a backup overwrites all current files of the extension without
   further confirmation. Make sure to load the correct extension before
   clicking :guilabel:`Restore backup`.
