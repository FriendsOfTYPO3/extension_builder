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

.. hint::

   By default, in case changes lead to unexpected results, Extension Builder
   saves a backup every time the extension is saved, which can be used to
   restore a previous state. More about backup configuration can be found on the
   ":doc:`/Configuration/Index`" page.
