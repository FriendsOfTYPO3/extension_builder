.. include:: /Includes.rst.txt
.. _saving-and-publishing:

====================================
Saving and publishing your extension
====================================

If your model represents the domain you wanted to implement you can hit the
:guilabel:`Save` button at the top.
The Extension Builder generates all required files for you in a location that
depends on your local setup:

..  tabs::

    ..  group-tab:: Composer mode

        If you run TYPO3 in :doc:`Composer mode <t3start:Installation/Install>`, you have to specify and configure a `local path repository <https://getcomposer.org/doc/05-repositories.md#path>`_ before saving your extension. Extension Builder reads the path from the TYPO3 project :file:`composer.json` and offers it as a target path to save the extension.

        The local path repository is normally configured as follows inside the `composer.json`:

        .. code-block:: js

           {
               "repositories": {
                   "packages": {
                       "type": "path",
                       "url": "packages/*"
                   }
               }
           }

        To install the extension in the TYPO3 instance you have to execute the following command:

        .. code-block:: bash

           composer require <extension-package-name>:@dev

        .. note::

           You only need the `@dev` dependency, if you install packages from a local path repository. If you install packages from a public repository (like `packagist.org <https://packagist.org/>`__ ), you can omit the `@dev` dependency or use a specific version like `^6.1.0`.

        The final command could be look like this:

        .. code-block:: bash

           composer require ebt/ebt-blog:@dev

        This will result into a symlink :file:`typo3conf/ext/<extension_key>/` to your extension and the extension to be recognized in the Extension Manager.

        .. hint::

            Before TYPO3 11.4 you had to install the extension additionally in the Extension Manager.

    ..  group-tab:: Legacy mode

        If you run TYPO3 in :doc:`Legacy mode <t3start:Installation/LegacyInstallation>` the extension will be generated directly at :file:`typo3conf/ext/<extension_key>/`.

        Once the extension is saved you should be able to install it in the Extension Manager.
