.. include:: /Includes.rst.txt

.. _knownissues:

============
Known issues
============

**UserTsConfig options.enableBookmarks**

With the UserTsConfig `options.enableBookmarks = 0` the backend module of
extension_builder is not working anymore.

See https://github.com/FriendsOfTYPO3/extension_builder/issues/632 for further
information.

*Conclusion*

.. warning::
   Don't use `options.enableBookmarks = 0` in your UserTsConfig for now.

