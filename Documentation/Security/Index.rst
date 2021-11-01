.. include:: /Includes.rst.txt

.. _security:

========
Security
========

Check access of controller actions
==================================

Be aware that any controller action can be called just by appending a parameter
to the URL where the frontend plugin or backend module is included:

:samp:`?tx_<extensionkey>_<pluginkey>[action]=<action>`

For example, if a controller provides an edit and a delete action,
the edit action URL could look like this:

:samp:`https://example.org/diary/?tx_blog_blogs[action]={edit}&tx_blog_blogs[blog]=2`

This will load the edit view with a form to edit the blog and load the blog data into it.

By default there is no access control. If someone manipulates this URL and replaces the edit action with the delete action:

:samp:`https://example.org/diary/?tx_blog_blogs[action]={delete}&tx_blog_blogs[blog]=2`

the blog would be deleted instead of being edited.

You have to make sure in your receiving controller to only allow context-specific
actions and generally restrict access to critical actions.


