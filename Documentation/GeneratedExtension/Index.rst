.. include:: /Includes.rst.txt
.. _generated-extension:

===================
Generated extension
===================

The generated extension looks like this, following the blog example from the
previous chapter:

.. code-block:: none

   .
   └── ebt_blog/
       ├── composer.json
       ├── ext_emconf.php
       ├── ext_localconf.php
       ├── ext_tables.php
       ├── ext_tables.sql
       ├── ExtensionBuilder.json
       ├── Classes/
           ├── Controller/..
           └── Domain/
               ├── Model/..
               └── Repository/..
       ├── Configuration/
           ├── ExtensionBuilder/..
           ├── FlexForms/..
           ├── TCA/..
           └── TypoScript/..
       ├── Documentation/..
       ├── Resources/
           ├── Private/
               ├── Language/..
               ├── Layouts/..
               ├── Partials/..
               └── Templates/..
           └── Public/
               └── Icons/..
It is explained in more detail in the following sections:

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 2
   :local:

.. _extension-skeleton:

Extension skeleton
==================

This is the minimum set of extension files generated when only the required
metadata has been entered into the properties form and no domain modeling has
been performed:

.. code-block:: none

   .
   └── ebt_blog/
       ├── composer.json
       ├── ext_emconf.php
       ├── ExtensionBuilder.json
       ├── Configuration/
           └── ExtensionBuilder/..
       ├── Documentation/..
       └── Resources/
           └── Public/
               └── Icons/
                   └── Extension.svg

The extension metadata is stored in the :file:`composer.json` and :file:`ext_emconf.php`
files and is used for installations in Composer mode and Legacy mode
respectively.
The extension icon :file:`Extension.svg` is displayed in the list of extensions
of the Extension Manager module.
The :file:`Documentation/` folder contains a basic set of documentation files.
Read the section ":ref:`documentation`" how to proceed with the documentation.

The Extension Builder stores some internal data in the :file:`ExtensionBuilder.json`
file and in the :file:`Configuration/ExtensionBuilder/` folder which should be
kept as long as the extension is edited in the Extension Builder.

.. _domain-modeling-files:

Domain modeling files
=====================

Most of the extension files are created for modeling the domain and configuring
frontend plugins and backend modules:

.. code-block:: none

   .
   └── ebt_blog/
       ├── ext_localconf.php
       ├── ext_tables.php
       ├── ext_tables.sql
       ├── Classes/
           ├── Controller/..
           └── Domain/
               ├── Model/..
               └── Repository/..
       ├── Configuration/
           ├── TCA/..
           └── TypoScript/..
       ├── Resources/
           ├── Private/
               ├── Language/..
               ├── Layouts/..
               ├── Partials/..
               └── Templates/..
           └── Public/
               └── Icons/..

The frontend plugins are registered in the :file:`ext_localconf.php` file and
the backend modules are registered in the :file:`ext_tables.php` file.
The associated views are configured in the :file:`Configuration/TypoScript/`
folder and the Fluid view templates are bundled in the :file:`Resources/Private/`
folder.

The database schema of the domain model is defined in the :file:`ext_tables.sql`
file.
The controller classes are provided in the folder :file:`Classes/Controller/`,
the classes that define the domain model are provided in the
folder :file:`Classes/Domain/` and their representation in the TYPO3 backend
is configured in the folder :file:`Configuration/TCA/`.
Last but not least, the tests of the classes are located in the folder
:file:`Tests/`.

For more information on tests, see the section ":ref:`tests`" and for everything
else, please refer to the :doc:`Extbase & Fluid book <t3extbasebook:Index>` of
the official TYPO3 documentation.

.. _documentation:

FlexForms for Plugins
=====================
The Extension Builder generates a FlexForm for each plugin. The FlexForm is
configured in the :file:`Configuration/FlexForms/` folder and is used to
configure the plugin in the TYPO3 backend. The FlexForm is a XML file that
defines the fields and their properties. The FlexForm is used to configure the
plugin in the TYPO3 backend.

The FlexForm is only created at the first time the plugin is created. If you
change the plugin configuration in the Extension Builder, the FlexForm is not
updated. You have to delete the FlexForm manually and then create the plugin
again.

.. admonition:: <BlaBlub>
   The generated FlexForm  only contains one example field. You can add more fields
   to the FlexForm by editing the XML file.

Documentation
=============

The Extension Builder has already created sample documentation for your
extension if you have :guilabel:`Generate documentation` enabled in
the properties form.

.. _writing-documentation:

Writing documentation
---------------------

The generated documentation is written in the *reStructuredText* (reST)
markup language with support for *Sphinx directives* and provides a typical
documentation structure with some dummy entries. More about how to document with
reStructuredText and Sphinx can be found in the official TYPO3 documentation:

* :ref:`introduction to reST & Sphinx <h2document:writing-rest-introduction>`
* :ref:`h2document:rest-cheat-sheet`
* :ref:`h2document:format-rest-cgl`

.. _render-documentation:

Render documentation
--------------------

Once you have made changes to the documentation files, you should render them
locally to test the output. The recommended method is to use the official
TYPO3 Documentation Team Docker image, but you can also install all the required
rendering tools from scratch. You can find more about this in the official TYPO3
documentation on the page ":doc:`h2document:RenderingDocs/Index`".

For example, on a Linux host with Docker installed, rendering boils down to
these commands:

.. code-block:: bash

   cd <path-to-extension>
   source <(docker run --rm t3docs/render-documentation show-shell-commands)
   dockrun_t3rd makehtml
   xdg-open "Documentation-GENERATED-temp/Result/project/0.0.0/Index.html"

.. _publish-documentation:

Publish documentation
---------------------

If you publish the extension to the *TYPO3 Extension Repository* (TER), do not
put the rendered documentation under version control, as the documentation will
be registered during the :doc:`publishing process </PublishToTer/Index>` for
automatic rendering and deployment to
:samp:`https://docs.typo3.org/p/<vendor-name>/<extension-name>/<version>/<language>/`
, for example to
:samp:`https://docs.typo3.org/p/friendsoftypo3/extension-builder/11.0/en-us/`.

If the extension is for private use, you are free to do anything with the
rendered documentation - including, of course, putting it under version control.

.. _tests:

Tests
=====

..  versionchanged:: 12.0.0
    The extension_builder does not generate tests for the extension anymore.

    Since the tests were a little bit confusing for the users, we decided to remove them from the generated extension. We recommend to write tests for your extension by yourself.

The TYPO3 Core is covered by thousands of tests of varying complexity:
Unit tests (testing part of a class), functional tests (testing multiple classes
in combination) and acceptance tests (testing the entire website user
experience). To simplify testing, the general functionality for writing tests is
bundled in the `TYPO3 Testing Framework <https://github.com/TYPO3/testing-framework>`__,
and all custom tests should use it by inheriting from its base classes.

If you are new to testing, we recommend that you invest
some time to learn the benefits of software testing. It will certainly improve
the quality of your software, but it will also boost your programming skills.
Moreover, it will allow you to refactor without fear of breaking anything:
Code that is covered by tests shows less unexpected behavior after refactoring.
