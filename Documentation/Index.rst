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
   Extension Builder Team

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

.. _Extbase: https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ExtensionArchitecture/Extbase/Index.html
.. _Fluid: https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ApiOverview/Fluid/Introduction.html

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Introduction <introduction>`

         .. container:: card-body

            What is the extension builder?

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Installation <installation>`

         .. container:: card-body

            Install the Extension Builder extension.

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Configuration <configuration>`

         .. container:: card-body

            Configuration of the Extension Builder.

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Graphical editor <graphical-editor>`

         .. container:: card-body

            Overview of the graphical editor.

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Generated extension <generated-extension>`

         .. container:: card-body

            What do you get after generating the extension?

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Security <security>`

         .. container:: card-body

            Keep Security in mind.

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Publishing to TER <publishing-to-ter>`

         .. container:: card-body

            How to publish your extension to the TER or Packagist.

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Roundtrip mode <roundtrip-mode>`

         .. container:: card-body

            When do you need the roundtrip mode?

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: :ref:`Contribution mode <contribution>`

         .. container:: card-body

            How you can help with developing the Extension Builder.

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
   ChangeLog/Index
   Sponsoring/Index
   Development/Index

.. Meta Menu

.. toctree::
   :hidden:

   Sitemap
