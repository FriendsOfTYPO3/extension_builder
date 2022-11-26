.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

There are two places to configure the Extension Builder:

1. Globally, in the *Extension Configuration* module of the TYPO3
   backend.

2. Locally, in the file :file:`Configuration/ExtensionBuilder/settings.yaml`
   of the generated extension.

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 2
   :local:

.. _global-configuration:

Global configuration
====================

In the TYPO3 backend go to :guilabel:`Settings > Extension Configuration` and
open the configuration of the Extension Builder. Here are several settings
configurable:

+----------------------------+-------------------------------------------------------------------------------+------------------------------------+
|**Setting**                 |**Description**                                                                |**Default**                         |
+----------------------------+-------------------------------------------------------------------------------+------------------------------------+
|Enable roundtrip mode       |If you enable the *roundtrip mode*, you can modify the generated files and     |true                                |
|                            |your changes will be preserved even if you customize the extension again in    |                                    |
|                            |the Extension Builder. For more information on the roundtrip mode, see the     |                                    |
|                            |page ":doc:`/InDepth/Roundtrip`".                                              |                                    |
|                            |                                                                               |                                    |
|                            |If you disable it (*kickstart mode*), all files are regenerated every time you |                                    |
|                            |save in the Extension Builder.                                                 |                                    |
+----------------------------+-------------------------------------------------------------------------------+------------------------------------+
|Backup on save              |The Extension Builder creates a backup of the extension every time it is saved |true                                |
|                            |if this option is set to true.                                                 |                                    |
+----------------------------+-------------------------------------------------------------------------------+------------------------------------+
|Backup directory            |The directory where the Extension Builder stores the backup –                  |var/tx_extensionbuilder/backups     |
|                            |specified as an absolute path or relative to ``PATH_site``.                    |                                    |
+----------------------------+-------------------------------------------------------------------------------+------------------------------------+

.. _local-configuration:

Local configuration
===================

After the initial creation of an extension, you will find the file
:file:`Configuration/ExtensionBuilder/settings.yaml` in the extension which
contains the following extension specific settings:

.. _overwrite-settings:

Overwrite settings
------------------

.. note::

   These settings only apply if the roundtrip mode is enabled in the global
   configuration.

The nesting reflects the file structure and a setting applies recursively to all
files and subfolders of a file path.

+----------------------------+-------------------------------------------------------------------------------+
|**Setting**                 |**Description**                                                                |
+----------------------------+-------------------------------------------------------------------------------+
|merge                       |All properties, methods and method bodies of class files are modified          |
|                            |and not overwritten.                                                           |
|                            |                                                                               |
|                            |Existing keys and identifiers in language files are preserved.                 |
|                            |                                                                               |
|                            |In any other file you will find a split token at the end of the file.          |
|                            |The part before this token is overwritten, the part after is preserved.        |
+----------------------------+-------------------------------------------------------------------------------+
|keep                        |These files are never overwritten.\*                                           |
+----------------------------+-------------------------------------------------------------------------------+
|skip                        |These files are not created.\*                                                 |
+----------------------------+-------------------------------------------------------------------------------+

.. warning::

   \* These settings may break the functionality to edit your extension in the
   Extension Builder! Handle with care!

This is an example of the :file:`settings.yaml` file:

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

.. _class-building:

Class building
--------------

By default, the generated controller, domain object and repository classes
inherit from the corresponding Extbase classes. It might be useful to inherit
from your own classes - which should then extend the Extbase classes.

The nesting reflects the class hierarchy and is restricted to the classes

- Controller
- Model\\AbstractEntity
- Model\\AbstractValueObject
- Repository

with these options available:

+------------------------------------+-------------------------------------------------------------------------------+
|**Setting**                         |**Description**                                                                |
+------------------------------------+-------------------------------------------------------------------------------+
|parentClass                         |The fully qualified class name of the class to inherit from.                   |
+------------------------------------+-------------------------------------------------------------------------------+
|setDefaultValuesForClassProperties  |By default, the class builder initializes class properties with the default    |
|                                    |value of their type, for example integer types with 0, string types with "",   |
|                                    |etc.                                                                           |
|                                    |Set this option to false if class properties should not be initialized,        |
|                                    |for example if you want to distinguish whether a property is not yet set       |
|                                    |or has been explicitly set to the default value.                               |
+------------------------------------+-------------------------------------------------------------------------------+

This is an example of the :file:`settings.yaml` file:

.. code-block:: yaml

   classBuilder:
     Controller:
       parentClass: \Ebt\Blog\Controller\ActionController

     Model:
       AbstractEntity:
         parentClass: \Ebt\Blog\DomainObject\AbstractEntity

       AbstractValueObject:
         parentClass: \Ebt\Blog\DomainObject\AbstractValueObject

     Repository:
       parentClass: \Ebt\Blog\Persistence\Repository

     setDefaultValuesForClassProperties: true

.. _miscellaneous:

Miscellaneous
-------------

There are more options both for the timestamps of the language files and for
working with the Extension Builder itself.

+----------------------------+-------------------------------------------------------------------------------+
|**Setting**                 |**Description**                                                                |
+----------------------------+-------------------------------------------------------------------------------+
|staticDateInXliffFiles      |By default, the date attribute in language files is updated every time you     |
|                            |save in the Extension Builder.                                                 |
|                            |This can be confusing in a version control system if all language files are    |
|                            |marked as changed even if no labels have been added or changed.                |
|                            |To prevent this effect, you can set a static date –                            |
|                            |although this is not recommended because the modification date can be useful   |
|                            |in the translation context.                                                    |
+----------------------------+-------------------------------------------------------------------------------+
|ignoreWarnings              |Some modeling configurations result in warnings.                               |
|                            |For example, if you configure a show action as a default action, you are       |
|                            |warned that you need to define a parameter of a domain object to be shown.     |
|                            |However, there may be use cases where you want to ignore the warning and thus  |
|                            |prevent it from appearing every time you save. Add the warning code that will  |
|                            |be displayed with the warning to the list of this setting. Each code should be |
|                            |listed on its own line and indented by 2 spaces.                               |
+----------------------------+-------------------------------------------------------------------------------+

This is an example of the :file:`settings.yaml` file:

.. code-block:: yaml

   staticDateInXliffFiles: 2021-11-18T12:37:00Z

   ignoreWarnings:
     503
