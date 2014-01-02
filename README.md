
This is a preview of the ExtensionBuilder with integrated [`PHP-Parser`][1]

The whole PHP parsing and code generation is much more reliable and controllable now, but there are some drawbacks:

Since the whole syntax is parsed into a node tree and comments are attached to nodes, single line comments which are
not related to a syntax node will be lost.

Whitespaces like linebreaks, tabs etc. are ignored in the original Parser. The PrinterService extends the original
printer class, to enable at least multi-line method parameter and a multi-line notation of arrays. But there is no way
to preserve the original number of indentations of the parsed source code.

On the other hand, we can modify the output according to the TYPO3 CGL. For example an if-statement with missing space
like if($foo) is printed as if ($foo) - the missing space is added by the printer.

You will find several tests in [`Tests/Functional/ParseAndPrint.php`][2]. There you can easily add Tests if you want to
test a certain formatting. The PrinterService can be overriden by the default Extbase object registration to enable
custom format conventions.

 [1]: https://github.com/nikic/PHP-Parser
 [2]: https://github.com/nicodh/extension_builder/blob/php-parser/Tests/Functional/ParseAndPrintTest.php