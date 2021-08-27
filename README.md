# TYPO3 Extension "extension_builder"

[![Build Status](https://github.com/FriendsOfTYPO3/extension_builder/workflows/tests/badge.svg)](https://github.com/FriendsOfTYPO3/extension_builder/actions)
[![Total Downloads](https://poser.pugx.org/friendsoftypo3/extension-builder/d/total.svg)](https://packagist.org/packages/friendsoftypo3/extension-builder)
[![Latest Stable Version](https://poser.pugx.org/friendsoftypo3/extension-builder/v/stable.svg)](https://packagist.org/packages/friendsoftypo3/extension-builder)
[![License](https://poser.pugx.org/friendsoftypo3/extension-builder/license.svg)](https://packagist.org/packages/friendsoftypo3/extension-builder)
[![TYPO3](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)

The Extension Builder helps you build and manage your Extbase based TYPO3 extensions.

It ships a graphical editor to build your domain model and generates most of the boiler-plate code necessary for you.
This includes TCA, Models, Repositories, language files and other things.

You can find the **full documentation** on: https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/Index.html

## Roundtrip mode

The editing (or roundtrip) mode even allows to modify an existing extension (previously created by Extension Builder)
without loosing your manual changes. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/Developer/Roundtrip.html

Keep in mind though that the code created by Extension Builder is only a starting point for you actual implementation of
functionality and is in no sense "production ready"! For upgrading an extension to a newer TYPO3 version we recommend using
[TYPO3 Rector](https://github.com/sabbelasichon/typo3-rector).

## Usage

### Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```bash
composer require friendsoftypo3/extension-builder
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the [extension](https://extensions.typo3.org/extension/extension_builder) with the extension manager module in the TYPO3 backend.

#### Installation using git

For each TYPO3 core version there is a branch that matches the major version.
The master branch aims to be compatible with the latest development of the TYPO3 core.

Run the following command within your Composer based TYPO3 project:

```bash
composer require friendsoftypo3/extension-builder:dev-master
# or for a specific branch run:
composer require friendsoftypo3/extension-builder:9.x-dev
composer require friendsoftypo3/extension-builder:10.x-dev
```

## Making Extension Builder even better

You found a bug, you have a fix?

Don't hesitate to create an issue or a pull request. Any help is really welcome. Thanks.

### Compile scss

The preferred way is to use yarn but npm also works. In that case just replace `yarn` with `npm`.

```bash
cd Resources/Public/jsDomainModeling/
yarn install
yarn build-css
```
