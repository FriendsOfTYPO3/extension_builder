.. include:: /Includes.rst.txt
.. _domain-object:

======================
Create a domain object
======================

If you want to extend the extension skeleton to implement business logic, create
at least one domain object by dragging the gray :guilabel:`New Model Object` tile onto
the canvas.
Give it a meaningful name, which must be an UpperCamelCase, alphanumeric string,
for example "Blog".

3.a. Edit domain object settings
--------------------------------

Edit the general settings of the domain object by opening the
:guilabel:`Domain object settings` subsection.


..  confval:: Is aggregate root?

    Check this option if this domain object combines other objects into an aggregate. Objects  outside the aggregate may contain references to this root object, but not to other objects  in the aggregate. The aggregate root checks the consistency of changes in the aggregate. An example is a blog object that has a related post object that can only be accessed through  the blog object with ``$blog->getPosts()``.

    Checking this option in the Extension Builder means that a controller class is generated for  this object, whose actions can be defined in the following :guilabel:`Default actions` subsection. Additionally, a repository class is generated that allows to retrieve all instances of this domain object from the persistence layer, i.e. in most scenarios from the database.

..  confval:: Description

    The domain object description can be any text. It is used in the PHPDoc comment of its class

..  confval:: Object type

    Select whether the domain object is an *entity* or a *value object*.

    An entity is identified by a unique identifier and its properties usually change during the application run. An example is a customer whose name, address, email, etc. may change, but can always be identified by the customer ID.

    A value object is identified by its property values and is usually used as a more complex entity property. An example is an address that is identified by its street, house number, city and postal code and would no longer be the same address if any of its values changed.

    .. note::

        **Note**: As of TYPO3 v11, it is recommended to specify any domain object of type "entity" due to the implementation details of Extbase. However, this might change in upcoming TYPO3 versions.

..  confval:: Map to existing table

    Instead of creating a new database table for the domain object, you can let it use an existing table. This can be useful if there is no domain object class using this table yet. Enter the appropriate table name in this field. Each object property will be assigned to an existing table field if both names match, otherwise the table field will be created. This check takes into account that the property name is a lowerCamelCase and the table field name is a lowercase, underscored string, for example the ``firstName`` property matches the ``first_name`` field. For more information on this topic, see the page :doc:`/InDepth/ExtendingDomainObjects`.

    An example is "tt_address". .

..  confval:: Extend existing model class

    Extbase supports *single table inheritance*, which means that a domain object class can inherit from another domain object class and instances of both are persisted in the same database table, optionally using only a subset of the table fields. The class to inherit from must be specified as a fully qualified class name and must exist in the current TYPO3 instance.

    Each object property will be assigned to an existing table field if both names match, otherwise the table field will be newly created. Additionally, a table field :sql:`tx_extbase_type` is created to specify the domain object class stored in this table row. For more information on this topic, see the :doc:`/InDepth/ExtendingDomainObjects` page.

    An example is "\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Category".

..  figure:: /Images/AutomaticScreenshots/DomainObjectSettings.png
    :alt: Domain object settings
    :width: 250px

    Domain object settings

3.b. Add actions
----------------

If the domain object is an aggregate root, open the :guilabel:`Default actions` section
and select the actions you need and add custom actions if required.
All selected actions are made available in the controller class that is created
along with the domain object class, and a Fluid template with an appropriate name is
generated for each action.

..  figure:: /Images/AutomaticScreenshots/Actions.png
    :alt: Default actions
    :width: 250px

    The associated controller actions


3.c. Add properties
-------------------

Expand the :guilabel:`properties` subsection to add domain object properties:

.. t3-field-list-table::
 :header-rows: 0

 - :Field:
         **Property name**
   :Description:
        The property name must be a lowerCamelCase, alphanumeric string. It is used

        - in language files and domain object classes as ``<propertyName>`` and
        - in the database table as ``<property_name>``.

        An example is "firstName".
 - :Field:
         **Property type**
   :Description:
        Select the type of the property. This determines the field type in the database table, the TCA type for TYPO3 backend rendering, and the Fluid type for TYPO3 frontend rendering.

        .. note::

            As of TYPO3 v11, the types marked with an asterisk (\*) are not fully implemented  for frontend rendering for various reasons. For example, the frontend handling of the types "file" and "image" is not yet implemented, because an implementation in Extbase is missing. For these, many implementation examples can be found on the Internet.
 - :Field:
        **Description**
        (advanced options)
   :Description:
        The property description can be any text. It is displayed in the *List* module of the TYPO3 backend as context sensitive help when you click on the property field.
 - :Field:
        **Is required?**
        (advanced options)
   :Description:
        Enable this option if this property must be set in TYPO3 frontend. Required properties are provided with a :php:`@TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")` PHPDoc annotation in the domain object class.
 - :Field:
        **Is exclude field?**
        (advanced options)
   :Description:
        Enable this option if you want to be able to hide this property from non-administrators in the TYPO3 backend.

..  figure:: /Images/AutomaticScreenshots/ObjectProperties.png
    :alt: Object properties
    :width: 250px

    The domain object properties

3.d. Add relations
------------------

If you create multiple domain objects you may want to connect them by relations.
A relation property can be added in the :guilabel:`relations` subsection.
When being added, it can be connected to the related object by dragging the
round connector of the relation property and dropping it at the connector of the
related object. When expanding the relation property panel you can refine the
type of relation.

.. t3-field-list-table::
 :header-rows: 0

 - :Field:
         **Name**
   :Description:
        The relation property name must be a lowerCamelCase, alphanumeric string. It is used like an ordinary property.
 - :Field:
         **Type**
   :Description:
        These relation types are available:

        **one-to-one (1:1)**

        This relation property can be associated with a specific instance of the related domain object and that instance has no other relation. An example is a person who has only one account and this account is not used by any other person.

        This setting results in a side-by-side selection field with a maximum of 1 selected item in the TYPO3 backend.

        **one-to-many (1:n)**

        This relation property can be associated with multiple instances of the related domain object, but each of those instances has no other relation. An example is a blog with multiple posts, but each post belongs to only one blog.

        See *Render type* description for more details on the rendering of the property in the TYPO3 backend.

        **many-to-one (n:1)**

        This relation property can be associated with a specific instance of the related domain object, but that instance can have multiple relations. An example is when each person has a specific birthplace, but many people can have the same birthplace.

        This is represented in the TYPO3 backend as a side-by-side selection field with a maximum number of 1 selected item.

        **many-to-many (m:n)**

        This relation property can be associated with multiple instances of the related domain object, and each of these instances can also have multiple relations. An example is when a book may have multiple authors and each author has written multiple books.

        See *Render type* description for more details on the rendering of the property in the TYPO3 backend.

        .. note::

            **Note**: For the many-to-many relation to work properly, you must perform two additional tasks:

            1. Add a many-to-many relation property in the related domain object as well and connect it to this object.
            2. Match the database table name in the :ref:`MM property <t3tca:columns-select-properties-mm>` of the TCA files of both domain objects in the generated extension. If this is not the case, the relations of one object are stored in a different database table than the relations of the related object.
 - :Field:
         **Render type**
   :Description:
        This option is only available for the one-to-many and many-to-many relations and defines the display of the relation property field in the TYPO3 backend:

        **one-to-many (1:n)**

        This can be rendered either as a :doc:`side-by-side selection box <t3tca:ColumnsConfig/Type/Select/MultipleSideBySide/Index>` or as an :doc:`inline-relational-record-editing field <t3tca:ColumnsConfig/Type/Inline/Index>`.

        **many-to-many (m:n)**

        This can be represented as either a  :doc:`side-by-side selection box <t3tca:ColumnsConfig/Type/Select/MultipleSideBySide/Index>`, a :doc:`multi-select checkbox <t3tca:ColumnsConfig/Type/Select/CheckBox/Index>`, or a :doc:`multi-select selection box <t3tca:ColumnsConfig/Type/Select/SingleBox/Index>`.
 - :Field:
        **Description**
   :Description:
        The relation description can be any text. It is displayed in the *List* module of the TYPO3 backend as context sensitive help when you click on the relation property field.
 - :Field:
        **Is exclude field?**
        (advanced options)
   :Description:
        Enable this option if you want to be able to hide this relation property from non-administrators in the TYPO3 backend.
 - :Field:
        **Lazy loading**
        (advanced options)
   :Description:
        Should the related instances be loaded when an instance of this domain is created  (*eager loading*) or on demand (*lazy loading*). Lazy loading relation properties are provided with a :php:`@TYPO3\CMS\Extbase\Annotation\ORM\Lazy` PHPDoc annotation in the domain object class.

..  figure:: /Images/AutomaticScreenshots/ObjectRelations.png
    :alt: Object relations
    :width: 250px

    The domain object relations



