.. include:: ../Includes.txt


.. _developer:

================
Developer Corner
================

Target group: **Developers**

This is your opportunity to pass on information to other developers who may be using your extension.

Use this section to provide examples of code or detail any information that would be deemed relevant to a developer.

You may wish to explain how a certain feature was implemented or detail any changes that might of been
made to the extension.

.. _developer-hooks:

Hooks
=====

Possible hook examples. Input parameters are:

+----------------+---------------+---------------------------------+
| Parameter      | Data type     | Description                     |
+================+===============+=================================+
| $table         | string        | Name of the table               |
+----------------+---------------+---------------------------------+
| $field         | string        | Name of the field               |
+----------------+---------------+---------------------------------+

Use parameter :code:`$table` to retrieve the table name...

.. _developer-api:

API
===

How to use the API...

.. code-block:: php

   $stuff = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
      '\\Foo\\Bar\\Utility\\Stuff'
   );
   $stuff->do();

or some other language:

.. code-block:: javascript
   :linenos:
   :emphasize-lines: 2-4

   $(document).ready(
      function () {
         doStuff();
      }
   );