.. include:: /Includes.rst.txt
.. _installation:

============
Installation
============

Admin rights are required to install and activate the Extension Builder.

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 1
   :local:

.. _installation_composer:

Composer mode
=============

If your TYPO3 installation uses Composer, install the latest Extension Builder
through:

.. code-block:: bash

   composer require friendsoftypo3/extension-builder

.. tip::

   Although it will still be possible to install extensions in legacy mode,
   composer mode is still recommended.

.. warning::

   If you are in composer mode, you need to add at least one entry inside "repositories" in your composer.json file. Otherwise the extension_builder will fail to save your extension. The extension_builder will store your generated extension in this folder.

   ..  code-block:: php

    "repositories": [
        "local": {
            "type": "path",
            "url": "Packages/*"
        }
    ]

.. warning::

   If you are in composer mode, you need to add at least one entry inside "repositories" in your composer.json file. Otherwise the extension_builder will fail to save your extension. The extension_builder will store your generated extension in this folder.

   ..  code-block:: php

    "repositories": [
        "local": {
            "type": "path",
            "url": "Packages/*"
        }
    ]

Installing the extension prior to TYPO3 11.4
--------------------------------------------

Before TYPO3 11.4 it was still necessary to manually activate extensions
installed via Composer using the Extension Manager. Activate it as follows:

-  Navigate to :guilabel:`Admin Tools > Extensions > Installed Extensions`
-  Search for `extension_builder`
-  Activate the extension by clicking on the :guilabel:`Activate` button in the
   :guilabel:`A/D` column

.. _installation_legacy:

Legacy mode
===========

If you are working with a TYPO3 installation that does not use Composer, install
the extension in the Extension Manager as follows:

-  Navigate to :guilabel:`Admin Tools > Extensions > Get Extensions`.
-  Click on :guilabel:`Update now`
-  Search for `extension_builder`
-  Click :guilabel:`Import and install` on the side of the extension entry

and activate it:

-  Navigate to :guilabel:`Admin Tools > Extensions > Installed Extensions`
-  Search for `extension_builder`
-  Activate the extension by clicking on the :guilabel:`Activate` button in the
   :guilabel:`A/D` column

.. seealso::

   On pages ":doc:`Managing Extensions <t3start:Extensions/Management>`" and
   ":doc:`Managing Extensions - Legacy Guide <t3start:Extensions/LegacyManagement>`"
   both TYPO3 installation modes are explained in detail.
