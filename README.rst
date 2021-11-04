|BuildStatus|_ |TotalDownloads|_ |LatestStableVersion|_ |License|_ |TYPO3|_

.. |BuildStatus| image:: https://github.com/FriendsOfTYPO3/extension_builder/workflows/tests/badge.svg
   :alt: Build Status
.. _BuildStatus: https://github.com/FriendsOfTYPO3/extension_builder/actions

.. |TotalDownloads| image:: https://poser.pugx.org/friendsoftypo3/extension-builder/d/total.svg
   :alt: Total Downloads
.. _TotalDownloads: https://packagist.org/packages/friendsoftypo3/extension-builder

.. |LatestStableVersion| image:: https://poser.pugx.org/friendsoftypo3/extension-builder/v/stable.svg
   :alt: Latest Stable Version
.. _LatestStableVersion: https://packagist.org/packages/friendsoftypo3/extension-builder

.. |License| image:: https://poser.pugx.org/friendsoftypo3/extension-builder/license.svg
   :alt: License
.. _License: https://packagist.org/packages/friendsoftypo3/extension-builder

.. |TYPO3| image:: https://img.shields.io/badge/TYPO3-11-orange.svg
   :alt: TYPO3
.. _TYPO3: https://get.typo3.org/version/11

=====================================
TYPO3 Extension ``extension_builder``
=====================================

The *Extension Builder* helps you to develop a TYPO3 extension based on the
domain-driven MVC framework `Extbase <https://docs.typo3.org/m/typo3/book-extbasefluid/master/en-us/0-Introduction/Index.html>`__
and the templating engine `Fluid <https://docs.typo3.org/m/typo3/book-extbasefluid/master/en-us/8-Fluid/Index.html>`__.

It provides a graphical editor to define domain models and their relations
as well as associated controllers with basic actions.
It also provides a mask to define extension metadata, frontend plugins and
backend modules that use the previously defined controllers and actions.
Finally, it generates an extension with boilerplate code that can be installed
and further developed.

:Repository:  https://github.com/FriendsOfTYPO3/extension_builder
:Read online: https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/

**TODO: The following text should be moved into the documentation.**

Roundtrip mode
==============

Keep in mind though that the code created by Extension Builder is only a starting point for you actual implementation of
functionality and is in no sense "production ready"!

Making Extension Builder even better
====================================

You found a bug, you have a fix?

Don't hesitate to create an issue or a pull request. Any help is really welcome. Thanks.

Compile scss
============

The preferred way is to use yarn but npm also works. In that case just replace ``yarn`` with ``npm``.

.. code-block:: bash

   cd Resources/Public/jsDomainModeling/
   yarn install
   yarn build-css
