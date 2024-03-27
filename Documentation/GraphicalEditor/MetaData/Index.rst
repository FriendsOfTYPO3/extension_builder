.. include:: /Includes.rst.txt
.. _meta-data:

=============================
Insert meta data of extension
=============================

Enter meaningful meta data of your extension in the properties form (1) on the
left side.

Once you have filled in the required *Name*, *Vendor name*, *Key*, *Description* and *Status* fields,
you can click the :guilabel:`Save` button at the top to create the extension
skeleton in your file system based on your configuration.
Feel encouraged to save and optionally commit all your changes frequently.

..  confval:: Name

    The extension name can be any string and is used as ``title`` property in the extension configuration file :file:`ext_emconf.php`. It is displayed, for example, in the `TYPO3 Extension Repository (TER) <https://extensions.typo3.org/>`__ and the Extension Manager module.

    An example is "The EBT Blog".

..  confval:: Vendor name

    The vendor name must be an UpperCamelCase, alphanumeric string. It is used

    - in the namespace of PHP classes: ``<VendorName>\<ExtensionName>\<Path>\<To>\<ClassName>`` and
    - in the ``name`` property of the :file:`composer.json`: ``<vendorname>/<extension-key>``.

    An example is "Ebt".

..  confval:: Extension key

    The extension key must be a lowercase, underscored, alphanumeric string. If you want to publish your extension to the TER, it must be unique throughout the TER and is best composed of the vendor name and an extension specific name, such as ``<vendorname>_<extension_name>``, where it must not start with "tx\_", "pages\_", "sys\_", "ts\_language\_", and "csh\_". It is used

    - as extension directory name :file:`<extension_key>/`,
    - in the language files: ``product-name=<extension_key>`` and
    - in the :file:`composer.json`: ``name: <vendor-name>/<extension-key>``.

    An example is "ebt_blog".

..  confval:: Description
    :name: extension-description

    The extension description can be any text. It is used as ``description`` property in extension configuration files :file:`ext_emconf.php` and :file:`composer.json`.

..  confval:: Category

    Which category the extension belongs to.

..  confval:: Version

    A good versioning scheme helps to track the changes. We recommend `semantic versioning <https://semver.org/>`__ .

..  confval:: State

    The status indicates whether the extension has already reached a stable phase, or whether it is still in alpha or beta.

    Extensions with the state *excludeFromUpdates* will not be updated by the Extension Manager.

    .. hint::

        Extension with state *alpha* are not possible to be uploaded to the TER.

..  confval:: Source language

    The source language represents the source language for the `.xliff` translation files. It is common to use "en" for English.

..  confval:: Depends on

    This represents the extensions, which are required by the current extension. The required extensions are listed in the :file:`composer.json` and :file:`ext_emconf.php` files.

..  confval:: Authors

    There is a possibility to add developers or project managers here. These authors will be listed inside :file:`composer.json` and :file:`ext_emconf.php`

