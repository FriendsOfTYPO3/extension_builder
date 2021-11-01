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

.. _overview-of-the-roundtrip-features:

Overview of the roundtrip features
==================================

Consequences of various actions:

**If you change the extension key:**

*  the extension will be copied to a folder named like the new extension key

*  all classes and tables are renamed

*  your code should be adapted to the new names (not just overridden with the
   default code)

**Changing the vendor name is not yet supported.**

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
