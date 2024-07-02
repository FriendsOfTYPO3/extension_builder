.. include:: /Includes.rst.txt
.. _backend-modules:

=======================
Create a backend module
=======================

If your extension should provide a TYPO3 backend module,
add a module in the :guilabel:`Backend modules` subsection of the properties form.
It will then be available in the module menu on the left side of the TYPO3
backend.

.. t3-field-list-table::
 :header-rows: 0

 - :Field:
        **Name**
   :Description:
        The module name can be any string. It is currently used only internally in the Extension Builder, for example in validation results. An example is "EBT Blogs".
 - :Field:
        **Key**
   :Description:
        The module key must be a lowercase, alphanumeric string. It is used to identify the module of your extension. An example is "ebtblogs".
 - :Field:
        **Description**
   :Description:
        The module description can be any text. It is displayed in the *About* module of the TYPO3 backend.
 - :Field:
        **Tab label**
   :Description:
        The module name in the TYPO3 module menu can be any string.
 - :Field:
        **Main module**
   :Description:
        This is the module key of the section in the TYPO3 module menu to which the module is assigned. For example, "web" or "site".

        The following module locations are available:

        - :guilabel:`web`
            A pagetree will be rendered beside the module.
        - :guilabel:`file`
            A filetree will be rendered beside the module.
        - :guilabel:`site`
            Neither a pagetree, nor a filetree will be rendered beside the module.
        - :guilabel:`system`
            The module will be accessible from the system settings and **therefore only be available for system-administrators**.
        - :guilabel:`tools`
            Neither a pagetree, nor a filetree will be rendered beside the module.
        - :guilabel:`user`
            The module will be accessible from the user settings.
        - :guilabel:`help`
            The module will be accessible from the help menu.
 - :Field:
        **Controller action combinations**
   :Description:
        In each line all actions of a controller supported by this module are listed by ``<controllerName> => <action1>,<action2>,...``. The first action of the first line is the default action. Actions are defined in the related aggregate root object, and the controller name corresponds to the object name.

        An example is

        .. code-block:: none

            Blog => list,show,new,create,edit,update,delete,duplicate
            Author => list,show,new,create,edit,update,delete

..  figure:: /Images/AutomaticScreenshots/BackendModule.png
    :alt: Backend module
    :width: 250px

    The backend modules
