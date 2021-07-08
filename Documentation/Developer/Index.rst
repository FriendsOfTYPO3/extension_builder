.. include:: ../Includes.txt


.. _developer:

Developer Corner
================

Target group: **Developers**

Please be aware that some configuration options are only available if the "Advanced options" is
activated by clicking on the link in the upper right corner in the modeler.

.. tip::
   The advanced options are enabled/disabled with the access key "a"


Use the advanced options with care. They are for special settings, 
useful for the experienced TYPO3 developer.


Creating a new extension
------------------------

Naming conventions
``````````````````

When to use uppercase, lowercase or CamelCase and other restrictions:

+-------------------+-----------------------------------------------------+-------------------+
|**Property**       |**Restriction**                                      |**Scope**          |
+-------------------+-----------------------------------------------------+-------------------+
|extension_key      |lowercase, alphanumeric, no space                    |Unique in TER      |
|                   |prefix not:\tx_\|u_\|user_\|pages_\sys_\|csh_        |                   |
+-------------------+-----------------------------------------------------+-------------------+
|Extension Name     |No restrictions                                      |No restrictions    |
+-------------------+-----------------------------------------------------+-------------------+
|ModelName          |UpperCamelCase                                       |Unique in extension|
+-------------------+-----------------------------------------------------+-------------------+
|propertyName       |lowerCamelCase                                       |Unique in model    |
+-------------------+-----------------------------------------------------+-------------------+
|Plugin Name        |No restrictions                                      |Unique in extension|
+-------------------+-----------------------------------------------------+-------------------+
|plugin_key         |No spaces, alphanumeric and -_                       |Unique in extension|
+-------------------+-----------------------------------------------------+-------------------+
|Backend Module Name|No restrictions                                      |Unique in extension|
+-------------------+-----------------------------------------------------+-------------------+
|backend_module_key |No spaces, alphanumeric and -_                       |Unique in extension|
+-------------------+-----------------------------------------------------+-------------------+


Extension meta data
```````````````````

Enter the basic meta data for the extension in the left panel of the modeler:

+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**Name**       |Enter here the name for your extension, that is displayed in the backend                                                   |
+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**Key**        |The extension key is a unique identifier for your extension                                                                |
+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**Description**|More details what the extension does                                                                                       |
+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**Version**    |A good versioning schema helps to track changes and versions. We recommend `Semantic Versioning <http://semver.org/>`_     |
+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**State**      |The state shows if the extension has already reached a stable phase, or if it is still in alpha or beta.                   |
+---------------+---------------------------------------------------------------------------------------------------------------------------+
|**Persons**    |Possibility to add developers or project managers                                                                          |
+---------------+---------------------------------------------------------------------------------------------------------------------------+


Adding plugins
``````````````

Every extension that should generate output in the frontend needs a plugin. It will be shown in the "plugin type" field
of the content element "Plugin". It can be added to your extension in the left panel:

+--------+--------------------------------------------------------+
|**Name**|A short, speaking name that is displayed in the backend |
+--------+--------------------------------------------------------+
|**Key** |A unique key to identify the plugin in your extension   |
+--------+--------------------------------------------------------+


Adding backend modules
``````````````````````

If your extension needs a module in the backend, this can be added in the left panel:

+---------------+--------------------------------------------------------------------+
|**Name**       |A short, speaking name that is displayed in the backend             |
+---------------+--------------------------------------------------------------------+
|**Key**        |A unique key to identify the module in the extension                |
+---------------+--------------------------------------------------------------------+
|**Description**|Description what the backend module does                            |
+---------------+--------------------------------------------------------------------+
|**Tab label**  |A tooltip that is displayed on mouseover                            |
+---------------+--------------------------------------------------------------------+
|**Main module**|The section in the backends left menu, where the module is located  |
+---------------+--------------------------------------------------------------------+

Creating a model
````````````````

Click on the "New Model Object" button in the upper left corner and drag it to a free place in the modeler.
Then click at the title bar and enter a name in UpperCamelCase. The model name must be unique in the extension.

Adding an entity/value object
'''''''''''''''''''''''''''''

It is selectable, if the model is an entity or a value object, and if it is an aggregate root.

Adding an action
''''''''''''''''

It can be defined which actions should be generated. If a model is an aggregate root, all default
`CRUD <http://de.wikipedia.org/wiki/CRUD>`_ actions can be added. Each action will result in a controller method 
and a template with the same name. Custom actions can be added later in the controller class.

Adding a property
'''''''''''''''''

Each property has these fields:

+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|**Field name**   |**Description**                          |**Results in the sources**                                                  |
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|Property name    |lowerCamelCase name                      |name of class property and a corresponding table column                     |
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|Property type    |vartype                                  |Some of the types are not completely implemented or configurable (see below)|
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|Description      |Is displayed as context sensitive help   |entry in locallang csh files                                                |
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|Is required      |Is this field required?                  |Validation notEmpty in Frontend and Backend forms                           |
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+
|Is exclude field|Is listed as exclude field in the backend |Non admin users won't see this field, except it is explicitly allowed       |
+-----------------+-----------------------------------------+----------------------------------------------------------------------------+


The frontend form handling of the property types file and image are not yet implemented, due to a missing implementation in Extbase.
The upload handling must be coded by the programmer! The property select list has no configuration options in the modeler
yet. The items list must be inserted into the TCA configuration.

Adding a relation
'''''''''''''''''

Relations connect two models. Here is a description of the fields:

+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|**Field name**  |**Description**                                                                        |**Results in the sources**                                           |
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|Property name   |lowerCamelCase name                                                                    |name of class property and a corresponding table column              |
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|Type            |database relation                                                                      |Explanation see below                                                |
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|Description     |Is displayed as context sensitive help                                                 |entry in locallang csh files                                         |
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|Is exclude field|Is listed as exclude field in the backend                                              |Non admin users won't see this field, except it is explicitly allowed|
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
|Lazy loading    |Should the related entries be loaded on instantiation of the model object or on request|these properties are tagged with a @lazy annotation                  |
+----------------+---------------------------------------------------------------------------------------+---------------------------------------------------------------------+


.. note::

   How the relations are implemented:

   **1:1 relation**
    Each parent class has only one (or none) child. Each child has only one parent. This setting will result in a field of type inline with maxitems=1

   **1:n relation**
    Each parent can have multiple children, but each child has only one parent This is also implemented as Inline (IRRE) field.

   **n:1 relation**
    Each parent has one child, but a child can have multiple parents This will result in a dropdown in the parents form
    in the backend. This relation is implemented with an MM relation table since TYPO3 can not handle multiple relations
    without comma separated values.

   **m:n relations**
    These are always implemented with a MM relation table and will result in a multi select in the backend

After adding a relation to the model it must be connected to the related model. This is achieved by a click 
on the round bullet near the relation name and by dragging it to the bullet at the top of the related model. (Not the other way round)

Saving the extension
''''''''''''''''''''

This is done by a click on the Save button at the bottom of the modeler. It might take some time, until all files are written to disk.
If an error occures, in most cases an error message will be displayed (for example in an invalid name was choosen).
Such an error must be fixed, before it can be saved. Be aware: the model configuration will be lost, if the browser window is closed before a successful saving.

The Extension Builder still has many limitations. The main missing features are:

+----------------------+----------------------------------------------------------------------------+
|TCA                   |Many TCA configurations are not yet configurable in the modeler             |
+----------------------+----------------------------------------------------------------------------+
|Extend existing models|There is no reliable implementation to extend existing models               |
+----------------------+----------------------------------------------------------------------------+
|SQL field types       |You can't fine tune the database field types                                |
+----------------------+----------------------------------------------------------------------------+
|Class hierarchy       |You can't extend models of extensions that are not installed                |
+----------------------+----------------------------------------------------------------------------+
|Non SQL persistence   |You can't create models that should not be persisted in the database        |
+----------------------+----------------------------------------------------------------------------+


.. toctree::
   :maxdepth: 2

   Roundtrip
   Unittests
   ExtendingModels
   Documentation
   PublishToTer
