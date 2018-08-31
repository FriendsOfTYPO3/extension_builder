.. include:: ../Includes.txt

Roundtrip mode
==============

Editing an existing extension
`````````````````````````````

Sooner or later it comes to the point, where you have to add or rename models but already changed code of the
originally created files.
This is, where "Roundtrip mode" is needed. It aims to preserve your manual changes and applying the new model
configuration at the same time.

The roundtrip mode is enabled by default. To disable it see the ExtensionBuilder Configuration :doc:`/Configuration/Index`

The general rule is: All stuff that is editable in the modeler should be applied in the modeler.
For example if you need another dependency in :file:`ext_emconf.php` you should add it in the modeler and not in :file:`ext_emconf.php` itself.

Be sure to configure the :ref:`configuration-overwritesettings`

Overview of the roundtrip features
``````````````````````````````````

Consequences of various actions:

**If you change the extension key:**

*   the extension will be copied to a folder named like the new extension key

*   all classes and tables are renamed

*   your code should be daptedt to the new names (not just overridden with the default code)


**Changing the Vendor name is not yet supported**


**If you rename a property:**

*   the corresponding class property is renamed

*   the corresponding getter and setter methods are updated

*   TCA files and SQL definitions are new generated, modifications will be LOST

*   existing data in the corresponding table column will be LOST, except you RENAME the column in the database manually

**If you rename a relation property:**

*   the corresponding class property is renamed

*   the corresponding getter/setter or add/remove methods are updated

*   the new SQL definition and default TCA configuration will be generated

*   existing data in the corresponding table column will be LOST, except you RENAME the column in the database manually

*   existing data in MM tables will be LOST, except you RENAME the mm table manually

**If you change a property type:**

*   the var type doc comment tags are updated

*   the type hint for the parameter in getter and setter methods are updated

*   the SQL type is updated if neccessary

*   existing data in the corresponding table column might get LOST, except you ALTER the column type in the database manually

**If you change a relation type:**

*   if you switch the type from 1:1 to 1:n or n:m or vice versa the getter/setter or add/remove methods will be lost (!)

*   the required new getter/setter/ or add/remove methods are generated with the default method body

*   existing data in MM tables will be LOST, except you RENAME the mm table manually

**If you rename a model:**

*   the corresponding classes (Model, Controller, Repository) will be renamed

*   all methods, properties and constants are preserved

*   relations to this model are updated

*   references to the renamed classes in OTHER classes are NOT updated (!)

*   TCA files and SQL definitions are new generated, modifications will be LOST

**Create a backup of your extension!**

If you don't use a versioning system like `git <http://git-scm.com>`_ or `svn <http://subversion.tigris.org>`_, this is perhaps the right moment to start using it
