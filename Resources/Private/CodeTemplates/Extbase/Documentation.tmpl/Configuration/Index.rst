.. include:: ../Includes.txt


.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

How is the extension configured? Aim to provide simple instructions detailing
how the extension is configured. Always assume that the user has no prior experience
of using your extension.

Try and provide a typical use case for your extension and detail each of the
steps required to get the extension running.


Typical Example
===============

- Do we need to include a static template?
- For example add a code snippet with comments

Minimal example of TypoScript:

- Code-blocks have support for syntax highlighting
- Use any supported language

.. code-block:: typoscript

   plugin.tx_myextension.settings {
      # configure basic email settings
      email {
         subject = Some subject
         from = someemail@domain.de
      }
   }

.. _configuration-typoscript:

TypoScript Reference
====================

When detailing data types or standard TypoScript
features, don't hesitate to cross-link to the TypoScript
Reference.

Information about how to use cross-references:
https://docs.typo3.org/typo3cms/HowToDocument/WritingReST/Hyperlinks.html

See the :file:`Settings.cfg` file for the declaration of cross-linking keys.
You can add more keys besides tsref.
