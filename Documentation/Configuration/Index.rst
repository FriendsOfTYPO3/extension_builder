.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

There are two places to configure the ExtensionBuilder:

1. in the extension manager configuration view for the ExtensionBuilder. These settings are applied "globally" in the TYPO3 instance.

2. in the file :file:`Configuration/ExtensionBuilder/settings.yaml` of your extension. These settings are extension specific.


.. _custom-documentation-renderUserDocumentation:


General extension builder configuration
```````````````````````````````````````

Go to the extension manager and open the configuration of the Extension Builder. Here several settings are configurable:

+----------------------------+-----------------------------------------------------------------------------------------------------------+--------------------------+
|**Setting**                 |**Impact**                                                                                                 |**Default**               |
+----------------------------+-----------------------------------------------------------------------------------------------------------+--------------------------+
|Enable edit mode (roundtrip)|If you don't set this to true, all files are newly generated each time you save your configuration         |false                     |
+----------------------------+-----------------------------------------------------------------------------------------------------------+--------------------------+
|Backup on each save         |The Extension Builder will generate a backup of the whole extension, if this is set to true                |true                      |
+----------------------------+-----------------------------------------------------------------------------------------------------------+--------------------------+
|Backup dir                  |The directory, where the Extension Builder will save the backup, as absolute path or relative to PATH_site |fileadmin/default/backups/|
+----------------------------+-----------------------------------------------------------------------------------------------------------+--------------------------+

Extension specific configuration
````````````````````````````````
After the initial creation of the extension, you will find the file :file:`Configuration/ExtensionBuilder/settings.yaml`
in your extension which contains the following extension specific configuration.

.. _configuration-overwritesettings:

Overwrite settings
------------------

These settings only apply if the roundtrip feature of the extension builder is enabled in the extension manager

The nesting reflects the file structure: a setting applies to a file or recursive to all files and subfolders.

merge:
	Impact on classes: All properties, methods and method bodies of the existing class will be **modified** according to the new settings but not **overwritten**

	Impact on locallang files: Existing keys and labels are always preserved (renaming in the GUI has only influence on the property and method names)

	Impact on any other file: You will find a Split token at the end of the file. After this token you can write whatever you want and it will be appended everytime the code is generated


keep:
	files are never overwritten\*



skip:
	files are not created\*


.. warning::

	\* These settings may break the functionality to edit your extension in the extension builder! Handle with care!


Here is an example:

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

  ext_icon.gif: keep

  Documentation.tmpl: skip

staticDateInXliffFiles: *2014-05-03T06:04:48Z*
 if not set (default) the date attribute in xliff files is updated each time you save a modeler configuration. If you
 use versioning systems (like git or svn) all xliff files are marked as changed then, even if there are no new labels.
 To avoid this you can set a static date. Be aware that the real date might be useful in translation context.

ignoreWarnings:
 Certain configurations cause a warning. For example if you configure a show action as default action the warning will say,
 you need a domainObject parameter that should be shown. However there might be use cases where you want such a configuration.
 To avoid the same warning on each save you can add the error code (which is displayed with the warning) to the list of
 ignoreWarnings. Just write one number per line indented 2 spaces

Settings for ClassBuilder
-------------------------

By default the Controller, Model and Repository classes inherit from the corresponding extbase class.
It might be useful to inherit from own classes (which should then extend the extbase classes).

Here you see the defaults:

.. code-block:: yaml

 classBuilder:

   Controller:
     parentClass: \TYPO3\CMS\Extbase\Mvc\Controller\ActionController

   Model:
     AbstractEntity:
       parentClass: \TYPO3\CMS\Extbase\DomainObject\AbstractEntity

     AbstractValueObject:
       parentClass: \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject

   Repository:
     parentClass: \TYPO3\CMS\Extbase\Persistence\Repository

   setDefaultValuesForClassProperties: true

setDefaultValuesForClassProperties: true
  By default the ClassBuilder will assign all generated class properties the default value of its corresponding property
  type. (e.g. 0 for integers etc.) Set this to false if you have a use case where you don't want this behaviour, for example
  when you have a property of type "string" and want to distinguish if it is not (yet) set (NULL) or an empty string ('').