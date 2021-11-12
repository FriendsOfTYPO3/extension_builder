.. include:: /Includes.rst.txt

===================
Generated extension
===================

.. _writing-documentation:

Writing documentation
=====================

The Extension Builder has already created sample documentation for your
extension if you have :guilabel:`Generate documentation template` enabled in
the property form.

Now rename the sample folder :file:`Documentation.tmpl/` to :file:`Documentation/`.

The generated documentation is written in the *reStructuredText* (reST)
markup language with support for *Sphinx directives* and provides a typical
documentation structure with some dummy entries. More about how to document with
reStructuredText and Sphinx can be found in the official TYPO3 documentation:

* :ref:`introduction to reST & Sphinx <h2document:writing-rest-introduction>`
* :ref:`h2document:rest-cheat-sheet`
* :ref:`h2document:format-rest-cgl`
* :ref:`h2document:rest-common-pitfalls`

Once you have made changes to the documentation files, you should render them
locally to test the output. The recommended method is to use the official
TYPO3 Documentation Team Docker image, but you can also install all the required
rendering tools from scratch. You can find more about this in the official TYPO3
documentation on the page ":doc:`h2document:RenderingDocs/Index`".

If you publish the extension to the *TYPO3 Extension Repository* (TER), do not
put the rendered documentation under version control, as the documentation will
be registered during the :doc:`publishing process </PublishToTer/Index>` for
automatic rendering and deployment to
:samp:`https://docs.typo3.org/typo3cms/extensions/<extension_name>/`.

If the extension is for private use, you are free to do anything with the
rendered documentation - including, of course, putting it under version control.
