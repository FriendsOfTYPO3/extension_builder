.. include:: /Includes.rst.txt

.. _development:

===========
Development
===========

If you want to participate in the development of the Extension Builder, set up
your local development environment as usual.

.. contents::
   :backlinks: top
   :class: compact-list
   :depth: 1
   :local:

.. _development-build-tooling:

Build tooling
=============

The JavaScript sources are bundled with `Vite <https://vitejs.dev/>`__.
To install dependencies and build the frontend assets, run:

.. code-block:: bash

   npm install
   npm run build

The compiled output is written to :file:`Resources/Public/JavaScript/`.

.. _development-linting:

Linting
-------

Code style is enforced by ESLint (JavaScript), Stylelint (SCSS) and Prettier
(formatting). Run all linters in one step:

.. code-block:: bash

   npm run lint

Individual linters can be invoked separately:

.. code-block:: bash

   npm run lint:js       # ESLint
   npm run lint:scss     # Stylelint
   npm run lint:format   # Prettier (check only)

To automatically fix formatting issues:

.. code-block:: bash

   npm run format

.. _development-e2e-tests:

E2E tests
=========

End-to-end tests are written with `Playwright <https://playwright.dev/>`__ and
require the ddev environment to be running:

.. code-block:: bash

   ddev start
   npm run test:e2e

To open the interactive Playwright UI:

.. code-block:: bash

   npm run test:e2e:ui

To run tests in a headed browser:

.. code-block:: bash

   npm run test:e2e:headed

.. _development-php-tests:

PHP tests
=========

PHP unit and functional tests use PHPUnit 10 and
`TYPO3 Testing Framework <https://github.com/TYPO3/testing-framework>`__ ^7.

Run unit tests:

.. code-block:: bash

   composer unit-tests

Run functional tests (uses SQLite, no database setup required):

.. code-block:: bash

   composer functional-tests

Run all checks (PHP CS Fixer, unit tests, functional tests):

.. code-block:: bash

   composer test

.. _development-rector:

Rector
======

For automated code migrations,
`TYPO3 Rector <https://github.com/sabbelasichon/typo3-rector>`__ is integrated.
Run it with:

.. code-block:: bash

   vendor/bin/rector

– *The TYPO3 project - Inspiring people to share*
