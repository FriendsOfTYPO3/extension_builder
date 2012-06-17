=========================
Configuration Reference
=========================

Technical information: Installation, Reference of TypoScript options,
configuration options on system level, how to extend it, the technical
details, how to debug it and so on.

Language should be technical, assuming developer knowledge of TYPO3.
Small examples/visuals are always encouraged.

Target group: **Developers**


TypoScript Reference
=====================

Possible subsections: Reference of TypoScript options.

.. ..................................
.. container:: table-row

	Property
		allWrap / +stdWrap

	Data type
		wrap

	Description
		Wraps the whole item.

	Default

.. ..................................
.. container:: table-row

	Property
		wrapItemAndSub

	Data type
		wrap

	Description
		Wraps the whole item and any submenu concatenated to it.

	Default

.. ..................................
.. container:: table-row

	Property
		subst_elementUid

	Data type
		boolean

	Description
		If set, all appearances of the string '{elementUid}' in the total element html-code (after wrapped in .allWrap} is substituted with the uid number of the menu item. This is useful if you want to insert an identification code in the HTML in order to manipulate properties with JavaScript.

	Default


FAQ
====

Possible subsection: FAQ
