.. include:: /Includes.rst.txt
.. _frontend-plugins:

=========================
Crreate a frontend plugin
=========================

If you want to create an extension that generates output in the TYPO3 frontend,
add a plugin in the :guilabel:`Frontend plugins` subsection of the properties form.
It will then be available for selection in the type field of the
TYPO3 content element "General Plugin".

.. t3-field-list-table::
 :header-rows: 0

 - :Field:
        **Name**
   :Description:
        The plugin name can be any string. It is displayed in the list of available plugins in the TYPO3 content element wizard. An example is "Latest articles".
 - :Field:
        **Key**
   :Description:
        The plugin key must be a lowercase, alphanumeric string. It is used to identify the plugin of your extension. An example is "latestarticles".
 - :Field:
        **Description**
   :Description:
        The plugin description can be any text. It is displayed in the list of available plugins in the TYPO3 content element wizard below the plugin name.
 - :Field:
        **Controller action combinations**
        (advanced options)
   :Description:
        In each line all actions of a controller supported by this plugin are listed by ``<controllerName> => <action1>,<action2>,...``. The first action of the first line is the default action. Actions are defined in the related aggregate root object, and the controller name corresponds to the object name.

        An example is

        .. code-block:: none

            Blog => list,show,new,create,edit,update
            Author => list,show
 - :Field:
        **Non cacheable actions**
        (advanced options)
   :Description:
        Each line lists all actions of a controller that should not be cached. This list is a subset of the *Controller action combinations* property list.

        An example is

        .. code-block:: none

            Blog => new,create,edit,update

..  figure:: /Images/AutomaticScreenshots/FrontendPlugin.png
    :alt: Frontend plugin
    :width: 250px

    The frontend plugins
