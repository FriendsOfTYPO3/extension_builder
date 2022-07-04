.. include:: /Includes.rst.txt

=================
Extension Builder
=================

:Extension key:
   extension_builder

:Package name:
   friendsoftypo3/extension-builder

:Version:
   |release|

:Language:
   en

:Author:
   Nico de Haen & contributors

:License:
   This document is published under the
   `Open Content License <https://www.openhub.net/licenses/opl>`__.

:Rendered:
   |today|

----

The Extension Builder helps you to develop a TYPO3 extension based on the
domain-driven MVC framework `Extbase`_ and the templating engine `Fluid`_.

It provides a graphical modeler to define domain objects and their relations
as well as associated controllers with basic actions. It also provides a
properties form to define extension metadata, frontend plugins and backend
modules that use the previously defined controllers and actions. Finally, it
generates a basic extension that can be installed and further developed.

In addition to the *kickstart mode*, the Extension Builder also provides a
*roundtrip mode* that allows you to use the graphical editor
even after you have started making manual changes to the files.
In this mode, the Extension Builder retains the manual changes,
such as new methods, changed method bodies, comments and annotations,
even if you change the extension in the graphical editor.

.. _Extbase: https://docs.typo3.org/m/typo3/book-extbasefluid/11.5/en-us/0-Introduction/Index.html
.. _Fluid: https://docs.typo3.org/m/typo3/book-extbasefluid/11.5/en-us/8-Fluid/Index.html

----

**Table of Contents:**

.. toctree::
   :maxdepth: 2
   :titlesonly:

   Introduction/Index
   Installation/Index
   GraphicalEditor/Index
   GeneratedExtension/Index
   Configuration/Index
   Security/Index
   PublishToTer/Index
   InDepth/Index
   Contribution/Index
   Sponsoring/Index
   Development/Index

.. Meta Menu

.. toctree::
   :hidden:

   Sitemap
   genindex
