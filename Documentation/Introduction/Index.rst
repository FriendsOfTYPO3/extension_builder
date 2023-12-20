.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

The *Extension Builder* helps you to develop a TYPO3 extension based on the
domain-driven MVC framework :doc:`Extbase <t3extbasebook:0-Introduction/Index>`
and the templating engine :doc:`Fluid <t3extbasebook:8-Fluid/Index>`.

Instead of creating an extension file structure from scratch,
let the graphical editor of the Extension Builder assist you:

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 1
   :local:

.. _what-does-it-do:

What does it do?
================

It provides a graphical modeler to define domain objects and their relations
as well as associated controllers with basic actions.
It also provides a properties form to define extension metadata, frontend
plugins and backend modules that use the previously defined controllers
and actions:

.. include:: /Images/AutomaticScreenshots/GraphicalEditorBlogExampleWindow.rst.txt

Finally, it generates a basic extension with that can be installed
and further developed:

.. code-block:: none

   .
   └── ebt_blog/
       ├── composer.json
       ├── ext_emconf.php
       ├── ext_localconf.php
       ├── ext_tables.php
       ├── ext_tables.sql
       ├── ExtensionBuilder.json
       ├── Classes/..
       ├── Configuration/..
       ├── Documentation/..
       ├── Resources/..
       └── Tests/..

In addition to the *kickstart mode*, the Extension Builder also provides a
*roundtrip mode* that allows you to use the graphical editor
even after you have started making manual changes to the files.
In this mode, the Extension Builder retains the manual changes,
such as new methods, changed method bodies, comments and annotations,
even if you change the extension in the graphical editor.

.. _what-does-it-not-do:

What does it not do?
====================

Custom TYPO3 content elements
-----------------------------

The Extension Builder focuses on the implementation of business logic in the
sense of *Domain-Driven Design*.
Unlike the deprecated *Kickstarter* extension, the Extension Builder is not
intended for creating your own TYPO3 content elements.
To create them, you should either use the Extension Builder to create a TYPO3
extension skeleton (without domain objects, controllers, plugins and modules)
and add
:doc:`TYPO3 content elements manually <t3coreapi:ApiOverview/ContentElements/AddingYourOwnContentElements>`,
or use one of the dedicate extensions like `Mask <https://extensions.typo3.org/extension/mask>`__
or `Dynamic Content Elements (DCE) <https://extensions.typo3.org/extension/dce>`__
instead.

Compatibility of existing extension with newer TYPO3
----------------------------------------------------

To make an existing TYPO3 extension compatible with a newer TYPO3 version,
we recommend using `TYPO3 Rector <https://github.com/sabbelasichon/typo3-rector>`__
instead of trying to load and save the extension in the Extension Builder of
the newer TYPO3 version.
