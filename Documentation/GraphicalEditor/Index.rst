.. include:: /Includes.rst.txt

.. _graphical-editor:

================
Graphical editor
================

To create a TYPO3 extension based on Extbase & Fluid, follow these steps:

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 2
   :local:

1. Open the graphical editor
============================

Go to the backend module of the Extension Builder,
switch to the graphical editor by selecting the :guilabel:`Domain Modelling`
view (1)
and ensure that the properties form (2) is expanded, located on the left side of
the graphical modeler (3).

Please note that some configuration options are only available if the advanced
options are enabled by clicking the :guilabel:`Show advanced options` button
in the upper right corner (4). These options are mainly intended for experienced
TYPO3 developers.

.. include:: /Images/AutomaticScreenshots/GraphicalEditor.rst.txt

2. Insert meta data of extension
================================

Enter meaningful meta data of your extension in the properties form (2) on the
left side.

Once you have filled in the required *Name*, *Vendor name* and *Key* fields,
you can click the :guilabel:`Save` button at the top to create the extension
skeleton in your file system based on your configuration.
Feel encouraged to save frequently.

+----------------------+----------------------------------------------------------------------------------------------------------+
|**Name**              |The extension name can be any string and is used as ``title`` property in the extension configuration     |
|                      |file :file:`ext_emconf.php`.                                                                              |
|                      |It is displayed, for example, in the `TYPO3 Extension Repository (TER) <https://extensions.typo3.org/>`__ |
|                      |and the Extension Manager module.                                                                         |
|                      |                                                                                                          |
|                      |An example is "The EBT Blog".                                                                             |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**Vendor name**       |The vendor name must be an UpperCamelCase, alphanumeric string. It is used                                |
|                      |                                                                                                          |
|                      |- in the namespace of PHP classes: ``<VendorName>\<ExtensionName>\<Path>\<To>\<ClassName>`` and           |
|                      |- in the ``name`` property of the :file:`composer.json`: ``<vendorname>/<extension-key>``.                |
|                      |                                                                                                          |
|                      |An example is "Ebt".                                                                                      |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**Key**               |The extension key must be a lowercase, underscored, alphanumeric string.                                  |
|                      |It must be unique throughout the TER and is best composed of the vendor name and an extension specific    |
|                      |name, such as ``<vendorname>_<extension_name>``, where it must not start with "tx\_", "pages\_", "sys\_", |
|                      |"ts\_language\_", and "csh\_". It is used                                                                 |
|                      |                                                                                                          |
|                      |- as extension directory name :file:`<extension_key>/`,                                                   |
|                      |- in the language files: ``product-name=<extension_key>`` and                                             |
|                      |- in the :file:`composer.json`: ``name: <vendor-name>/<extension-key>``.                                  |
|                      |                                                                                                          |
|                      |An example is "ebt_blog".                                                                                 |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**Description**       |The extension description can be any text. It is used as ``description`` property in extension            |
|                      |configuration files :file:`ext_emconf.php` and :file:`composer.json`.                                     |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**Version**           |A good versioning scheme helps to track the changes. We recommend *semantic versioning*.                  |
|(More options)        |                                                                                                          |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**State**             |The status indicates whether the extension has already reached a stable phase, or whether it is still in  |
|(More options)        |alpha or beta.                                                                                            |
+----------------------+----------------------------------------------------------------------------------------------------------+
|**Extension authors** |There is a possibility to add developers or project managers here.                                        |
+----------------------+----------------------------------------------------------------------------------------------------------+

3. Create a domain object
=========================

If you want to extend the extension skeleton to implement business logic, create
at least one domain object by dragging the gray :guilabel:`New Model Object` tile onto
the canvas.
Give it a meaningful name, which must be an UpperCamelCase, alphanumeric string,
for example "Blog".

3.a. Edit domain object settings
--------------------------------

Edit the general settings of the domain object by opening the
:guilabel:`Domain object settings` subsection.

+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Is aggregate root?**             |Check this option if this domain object combines other objects into an aggregate. Objects    |
|                                   |outside the aggregate may contain references to this root object, but not to other objects   |
|                                   |in the aggregate. The aggregate root checks the consistency of changes in the aggregate.     |
|                                   |An example is a blog object that has a related post object that can only be accessed through |
|                                   |the blog object with ``$blog->getPosts()``.                                                  |
|                                   |                                                                                             |
|                                   |Checking this option in the Extension Builder means that a controller class is generated for |
|                                   |this object, whose actions can be defined in the following :guilabel:`Default actions`       |
|                                   |subsection.                                                                                  |
|                                   |Additionally, a repository class is generated that allows to retrieve all instances of this  |
|                                   |domain object from the persistence layer, i.e. in most scenarios from the database.          |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Description**                    |The domain object description can be any text. It is used in the PHPDoc comment of its       |
|                                   |class.                                                                                       |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Object type**                    |Select whether the domain object is an *entity* or a *value object*.                         |
|(Advanced options)                 |                                                                                             |
|                                   |An entity is identified by a unique identifier and its properties usually change during the  |
|                                   |application run. An example is a customer whose name, address, email, etc. may change, but   |
|                                   |can always be identified by the customer ID.                                                 |
|                                   |                                                                                             |
|                                   |A value object is identified by its property values and is usually used as a more complex    |
|                                   |entity property. An example is an address that is identified by its street, house number,    |
|                                   |city and postal code and would no longer be the same address if any of its values changed.   |
|                                   |                                                                                             |
|                                   |**Note**: As of TYPO3 v11, it is recommended to specify any domain object of type "entity"   |
|                                   |due to the implementation details of Extbase. However, this might change in upcoming TYPO3   |
|                                   |versions.                                                                                    |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Map to existing table**          |Instead of creating a new database table for the domain object, you can let it use an        |
|(Advanced options)                 |existing table. This can be useful if there is no domain object class using this table yet.  |
|                                   |Enter the appropriate table name in this field. Each object property will be assigned to an  |
|                                   |existing table field if both names match, otherwise the table field will be created.         |
|                                   |This check takes into account that the property name is a lowerCamelCase and the table field |
|                                   |name is a lowercase, underscored string, for example the ``firstName`` property matches the  |
|                                   |``first_name`` field. For more information on this topic, see the page                       |
|                                   |":doc:`/InDepth/ExtendingDomainObjects`".                                                    |
|                                   |                                                                                             |
|                                   |An example is "tt_address".                                                                  |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Extend existing model class**    |Extbase supports *single table inheritance*, which means that a domain object class can      |
|(Advanced options)                 |inherit from another domain object class and instances of both are persisted in the same     |
|                                   |database table, optionally using only a subset of the table fields. The class to inherit     |
|                                   |from must be specified as a fully qualified class name and must exist in the current TYPO3   |
|                                   |instance.                                                                                    |
|                                   |Each object property will be assigned to an existing table field if both names match,        |
|                                   |otherwise the table field will be newly created. Additionally, a table field                 |
|                                   |:sql:`tx_extbase_type` is created to specify the domain object class stored in this table    |
|                                   |row. For more information on this topic, see the ":doc:`/InDepth/ExtendingDomainObjects`"    |
|                                   |page.                                                                                        |
|                                   |                                                                                             |
|                                   |An example is "\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Category".                              |
+-----------------------------------+---------------------------------------------------------------------------------------------+

.. include:: /Images/AutomaticScreenshots/DomainObjectSettings.rst.txt

3.b. Add actions
----------------

If the domain object is an aggregate root, open the :guilabel:`Default actions` section
and select the actions you need and add custom actions if required.
All selected actions are made available in the controller class that is created
along with the domain object class, and a Fluid template with an appropriate name is
generated for each action.

.. include:: /Images/AutomaticScreenshots/Actions.rst.txt

3.c. Add properties
-------------------

Expand the :guilabel:`properties` subsection to add domain object properties:

+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Property name**                  |The property name must be a lowerCamelCase, alphanumeric string. It is used                  |
|                                   |                                                                                             |
|                                   |- in language files and domain object classes as ``<propertyName>`` and                      |
|                                   |- in the database table as ``<property_name>``.                                              |
|                                   |                                                                                             |
|                                   |An example is "firstName".                                                                   |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Property type**                  |Select the type of the property. This determines the field type in the database table, the   |
|                                   |TCA type for TYPO3 backend rendering, and the Fluid type for TYPO3 frontend rendering.       |
|                                   |                                                                                             |
|                                   |**Note**: As of TYPO3 v11, the types marked with an asterisk (\*) are not fully implemented  |
|                                   |for frontend rendering for various reasons. For example, the frontend handling of the types  |
|                                   |"file" and "image" is not yet implemented, because an implementation in Extbase is missing.  |
|                                   |For these, many implementation examples can be found on the Internet.                        |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Description**                    |The property description can be any text. It is displayed in the *List* module of the TYPO3  |
|(Advanced options)                 |backend as context sensitive help when you click on the property field.                      |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Is required?**                   |Enable this option if this property must be set in TYPO3 frontend. Required properties are   |
|(Advanced options)                 |provided with a :php:`@TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")` PHPDoc annotation  |
|                                   |in the domain object class.                                                                  |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Is exclude field?**              |Enable this option if you want to be able to hide this property from non-administrators      |
|(Advanced options)                 |in the TYPO3 backend.                                                                        |
+-----------------------------------+---------------------------------------------------------------------------------------------+

.. include:: /Images/AutomaticScreenshots/Properties.rst.txt

3.d. Add relations
------------------

If you create multiple domain objects you may want to connect them by relations.
A relation property can be added in the :guilabel:`relations` subsection.
When being added, it can be connected to the related object by dragging the
round connector of the relation property and dropping it at the connector of the
related object. When expanding the relation property panel you can refine the
type of relation.

+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Name**                           |The relation property name must be a lowerCamelCase, alphanumeric string. It is used like an |
|                                   |ordinary property.                                                                           |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Type**                           |These relation types are available:                                                          |
|                                   |                                                                                             |
|                                   |**one-to-one (1:1)**                                                                         |
|                                   |                                                                                             |
|                                   |This relation property can be associated with a specific instance of the related domain      |
|                                   |object and that instance has no other relation. An example is a person who has only one      |
|                                   |account and this account is not used by any other person.                                    |
|                                   |                                                                                             |
|                                   |This setting results in a side-by-side selection field with a maximum of 1 selected item in  |
|                                   |the TYPO3 backend.                                                                           |
|                                   |                                                                                             |
|                                   |**one-to-many (1:n)**                                                                        |
|                                   |                                                                                             |
|                                   |This relation property can be associated with multiple instances of the related domain       |
|                                   |object, but each of those instances has no other relation. An example is a blog with         |
|                                   |multiple posts, but each post belongs to only one blog.                                      |
|                                   |                                                                                             |
|                                   |See *Render type* description for more details on the rendering of the property in the TYPO3 |
|                                   |backend.                                                                                     |
|                                   |                                                                                             |
|                                   |**many-to-one (n:1)**                                                                        |
|                                   |                                                                                             |
|                                   |This relation property can be associated with a specific instance of the related domain      |
|                                   |object, but that instance can have multiple relations. An example is when each person has    |
|                                   |a specific birthplace, but many people can have the same birthplace.                         |
|                                   |                                                                                             |
|                                   |This is represented in the TYPO3 backend as a side-by-side selection field with a maximum    |
|                                   |number of 1 selected item.                                                                   |
|                                   |                                                                                             |
|                                   |**many-to-many (m:n)**                                                                       |
|                                   |                                                                                             |
|                                   |This relation property can be associated with multiple instances of the related domain       |
|                                   |object, and each of these instances can also have multiple relations. An example is when a   |
|                                   |book may have multiple authors and each author has written multiple books.                   |
|                                   |                                                                                             |
|                                   |See *Render type* description for more details on the rendering of the property in the TYPO3 |
|                                   |backend.                                                                                     |
|                                   |                                                                                             |
|                                   |**Note**: For the many-to-many relation to work properly, you must perform two additional    |
|                                   |tasks:                                                                                       |
|                                   |                                                                                             |
|                                   |1. Add a many-to-many relation property in the related domain object as well and connect it  |
|                                   |   to this object.                                                                           |
|                                   |2. Match the database table name in the                                                      |
|                                   |   :ref:`MM property <t3tca:columns-select-properties-mm>` of the TCA files of both domain   |
|                                   |   objects in the generated extension. If this is not the case, the relations of one object  |
|                                   |   are stored in a different database table than the relations of the related object.        |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Render type**                    |This option is only available for the one-to-many and many-to-many relations and defines the |
|                                   |display of the relation property field in the TYPO3 backend:                                 |
|                                   |                                                                                             |
|                                   |**one-to-many (1:n)**                                                                        |
|                                   |                                                                                             |
|                                   |This can be rendered either as a                                                             |
|                                   |:doc:`side-by-side selection box <t3tca:ColumnsConfig/Type/Select/MultipleSideBySide/Index>` |
|                                   |or as an                                                                                     |
|                                   |:doc:`inline-relational-record-editing field <t3tca:ColumnsConfig/Type/Inline/Index>`.       |
|                                   |                                                                                             |
|                                   |**many-to-many (m:n)**                                                                       |
|                                   |                                                                                             |
|                                   |This can be represented as either a                                                          |
|                                   |:doc:`side-by-side selection box <t3tca:ColumnsConfig/Type/Select/MultipleSideBySide/Index>` |
|                                   |, a :doc:`multi-select checkbox <t3tca:ColumnsConfig/Type/Select/CheckBox/Index>`,           |
|                                   |or a :doc:`multi-select selection box <t3tca:ColumnsConfig/Type/Select/SingleBox/Index>`.    |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Description**                    |The relation description can be any text. It is displayed in the *List* module of the TYPO3  |
|                                   |backend as context sensitive help when you click on the relation property field.             |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Is exclude field?**              |Enable this option if you want to be able to hide this relation property from                |
|(Advanced options)                 |non-administrators in the TYPO3 backend.                                                     |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Lazy loading**                   |Should the related instances be loaded when an instance of this domain is created            |
|(Advanced options)                 |(*eager loading*) or on demand (*lazy loading*). Lazy loading relation properties are        |
|                                   |provided with a :php:`@TYPO3\CMS\Extbase\Annotation\ORM\Lazy` PHPDoc annotation in the       |
|                                   |domain object class.                                                                         |
+-----------------------------------+---------------------------------------------------------------------------------------------+

.. include:: /Images/AutomaticScreenshots/Relations.rst.txt

4. Create a frontend plugin
===========================

If you want to create an extension that generates output in the TYPO3 frontend,
add a plugin in the :guilabel:`Frontend plugins` subsection of the properties form.
It will then be available for selection in the type field of the
TYPO3 content element "General Plugin".

+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Name**                           |The plugin name can be any string. It is displayed in the list of available plugins in the   |
|                                   |TYPO3 content element wizard. An example is "Latest articles".                               |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Key**                            |The plugin key must be a lowercase, alphanumeric string. It is used to identify the plugin   |
|                                   |of your extension. An example is "latestarticles".                                           |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Description**                    |The plugin description can be any text. It is displayed in the list of available plugins in  |
|                                   |the TYPO3 content element wizard below the plugin name.                                      |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Controller action combinations** |In each line all actions of a controller supported by this plugin are listed by              |
|(Advanced options)                 |``<controllerName> => <action1>,<action2>,...``. The first action of the first line is the   |
|                                   |default action. Actions are defined in the related aggregate root object, and the controller |
|                                   |name corresponds to the object name.                                                         |
|                                   |                                                                                             |
|                                   |An example is                                                                                |
|                                   |                                                                                             |
|                                   |.. code-block:: none                                                                         |
|                                   |                                                                                             |
|                                   |   Blog => list,show,new,create,edit,update                                                  |
|                                   |   Author => list,show                                                                       |
|                                   |                                                                                             |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Non cacheable actions**          |Each line lists all actions of a controller that should not be cached. This list is a subset |
|(Advanced options)                 |of the *Controller action combinations* property list.                                       |
|                                   |                                                                                             |
|                                   |An example is                                                                                |
|                                   |                                                                                             |
|                                   |.. code-block:: none                                                                         |
|                                   |                                                                                             |
|                                   |   Blog => new,create,edit,update                                                            |
|                                   |                                                                                             |
+-----------------------------------+---------------------------------------------------------------------------------------------+

.. include:: /Images/AutomaticScreenshots/FrontendPlugins.rst.txt

5. Create a backend module
==========================

If your extension should provide a TYPO3 backend module,
add a module in the :guilabel:`Backend modules` subsection of the properties form.
It will then be available in the module menu on the left side of the TYPO3
backend.

+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Name**                           |The module name can be any string. It is currently used only internally in the               |
|                                   |Extension Builder, for example in validation results. An example is "EBT Blogs".             |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Key**                            |The module key must be a lowercase, alphanumeric string. It is used to identify the module   |
|                                   |of your extension. An example is "ebtblogs".                                                 |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Description**                    |The module description can be any text. It is displayed in the *About* module of the         |
|                                   |TYPO3 backend.                                                                               |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Tab label**                      |The module name in the TYPO3 module menu can be any string.                                  |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Main module**                    |This is the module key of the section in the TYPO3 module menu to which the module is        |
|                                   |assigned. For example, "web" or "site".                                                      |
+-----------------------------------+---------------------------------------------------------------------------------------------+
|**Controller action combinations** |In each line all actions of a controller supported by this module are listed by              |
|(Advanced options)                 |``<controllerName> => <action1>,<action2>,...``. The first action of the first line is the   |
|                                   |default action. Actions are defined in the related aggregate root object, and the controller |
|                                   |name corresponds to the object name.                                                         |
|                                   |                                                                                             |
|                                   |An example is                                                                                |
|                                   |                                                                                             |
|                                   |.. code-block:: none                                                                         |
|                                   |                                                                                             |
|                                   |   Blog => list,show,new,create,edit,update,delete,duplicate                                 |
|                                   |   Author => list,show,new,create,edit,update,delete                                         |
|                                   |                                                                                             |
+-----------------------------------+---------------------------------------------------------------------------------------------+

.. include:: /Images/AutomaticScreenshots/BackendModules.rst.txt

6. Save the extension
=====================

If your model represents the domain you wanted to implement you can hit the
:guilabel:`Save` button at the top.
The Extension Builder generates all required files for you in a location that
depends on your local setup:

6.a. Composer mode
------------------

If you run TYPO3 in :doc:`Composer mode <t3start:Installation/Install>`, you
have to specify and configure a `local path repository <https://getcomposer.org/doc/05-repositories.md#path>`_
before saving your extension. Extension Builder reads the path from the TYPO3
project :file:`composer.json` and offers it as a target path to save the
extension.

The local path repository is normally configured as follows:

.. code-block:: js

   {
       "repositories": {
           "packages": {
               "type": "path",
               "url": "packages/*"
           }
       }
   }

To install the extension in the TYPO3 instance you have to execute the usual:

.. code-block:: bash

   composer require <extension-package-name>:@dev

, for example:

.. code-block:: bash

   composer require ebt/ebt-blog:@dev

which will cause a symlink :file:`typo3conf/ext/<extension_key>/` to your extension
to be created and the extension to be recognized in the Extension Manager.

Before TYPO3 11.4 you had to install the extension additionally in the Extension
Manager.

6.b. Legacy mode
----------------

If you run TYPO3 in :doc:`Legacy mode <t3start:Installation/LegacyInstallation>`
the extension will be generated directly at :file:`typo3conf/ext/<extension_key>/`.

Once the extension is saved you should be able to install it in the Extension
Manager.

7. Continue developing
======================

Now you can start modifying the generated files in your IDE. If you still want
to be able to modify the domain model in the graphical editor you have to make
sure that the *roundtrip mode* is activated in the
:doc:`configuration </Configuration/Index>`, before loading the extension in the
Extension Builder again.

.. include:: /Images/AutomaticScreenshots/GraphicalEditorBlogExampleFullPage.rst.txt
