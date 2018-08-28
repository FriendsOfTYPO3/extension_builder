# TYPO3 Extension "extension_builder"

The Extension Builder helps you build and manage your Extbase based TYPO3 extensions.

It ships a graphical editor to build your domain model and generates most of the boiler-plate code necessary for you.
This includes TCA, Models, Repositories, language files and other things.

The editing (or roundtrip) mode even allows to modify an existing extension (previously created by Extension Builder) without loosing your manual changes. See https://docs.typo3.org/typo3cms/extensions/extension_builder/Developer/Roundtrip.html 

Keep in mind though that the code created by Extension Builder is only a starting point for you actual implementation of
functionality and is in no sense "production ready"!

## Which version of Extension Builder to use?

### The Extension Builder GIT version

For each TYPO3 core version there is a branch that matches the minor version.
The master branch of this repository aims to be compatible with the latest development of the TYPO3 core.

We encourage every developer to use the latest matching source directly from github or to load those via composer. 

### The Extension Builder TER version

The TER (TYPO3 Extension Repository) version of the Extension Builder is primary thought for people who want to have an easy introduction to learn how an extbase extension works.  There will be a release every once in a while, but to have an up-to-date maintenance it is suggested to rather stick to our recommendation above and use the github version.


## Making Extension Builder even better

You found a bug, You have a fix?

Don't hesitate to create an issue or push a pull request. Any help is really welcome. Thanks.
