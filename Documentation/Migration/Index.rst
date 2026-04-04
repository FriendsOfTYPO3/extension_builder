.. include:: /Includes.rst.txt

.. _migration:

=====================================
Migrating to TYPO3 13 / PHP 8.3
=====================================

This page documents the breaking changes and recommended patterns when
upgrading an extension that was generated with an older version of the
Extension Builder to TYPO3 13 (or when using the Extension Builder on
a TYPO3 13 installation).

.. _migration-requirements:

System Requirements
===================

.. list-table::
   :header-rows: 1

   * - Component
     - Required version
   * - TYPO3
     - ^13.0
   * - PHP
     - ^8.3
   * - Extension Builder
     - 13.x branch

.. _migration-generated-code:

Generated Code Changes
======================

The Extension Builder 13.x branch generates code that is compatible with
TYPO3 13 and PHP 8.3. If you have previously generated an extension with
an older version, you need to update the following patterns manually or
regenerate the affected files.

.. _migration-repository:

Repository Classes
------------------

**Before (TYPO3 11 generated):**

.. code-block:: php

   class ArticleRepository
   {
       protected $defaultOrderings = ['sorting' => QueryInterface::ORDER_ASCENDING];
   }

**After (TYPO3 13 generated):**

.. code-block:: php

   use TYPO3\CMS\Extbase\Persistence\Repository;

   class ArticleRepository extends Repository
   {
       protected array $defaultOrderings = ['sorting' => QueryInterface::ORDER_ASCENDING];
   }

.. attention::
   Without ``extends Repository`` the persistence manager cannot register your
   repository and all ``findAll()`` / ``findBy*()`` calls will fail.

.. _migration-model:

Model Classes
-------------

**Before (TYPO3 11 generated):**

.. code-block:: php

   class Article extends AbstractEntity
   {
       /**
        * @var string
        */
       protected $title;

       /**
        * @var ObjectStorage<Tag>
        */
       protected $tags;
   }

**After (TYPO3 13 generated):**

.. code-block:: php

   use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
   use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

   class Article extends AbstractEntity
   {
       protected string $title = '';
       protected ObjectStorage $tags;

       public function __construct()
       {
           $this->initializeObject();
       }

       public function initializeObject(): void
       {
           $this->tags ??= new ObjectStorage();
       }
   }

Key differences:

- Native PHP 8.3 property type declarations replace ``@var`` docblocks
- ``ObjectStorage`` is imported via ``use`` instead of fully qualified
- Default values are explicit (``string $title = ''``)
- ``initializeObject()`` uses ``??=`` (null-coalescing assignment)

.. _migration-controller:

Controller Classes
------------------

**Before (TYPO3 11 generated — setter injection):**

.. code-block:: php

   class ArticleController extends ActionController
   {
       /**
        * @var ArticleRepository
        */
       protected $articleRepository;

       public function injectArticleRepository(ArticleRepository $articleRepository): void
       {
           $this->articleRepository = $articleRepository;
       }
   }

**After (TYPO3 13 generated — constructor injection):**

.. code-block:: php

   use Psr\Http\Message\ResponseInterface;
   use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

   class ArticleController extends ActionController
   {
       public function __construct(
           private readonly ArticleRepository $articleRepository,
       ) {}

       public function listAction(): ResponseInterface
       {
           $articles = $this->articleRepository->findAll();
           $this->view->assign('articles', $articles);
           return $this->htmlResponse();
       }
   }

Key differences:

- Constructor injection with ``readonly`` properties replaces setter injection
- ``ResponseInterface`` is imported via ``use`` instead of fully qualified
- All action methods declare ``ResponseInterface`` as return type

.. _migration-tca:

TCA Configuration
-----------------

TYPO3 13 requires a ``type`` field in every TCA table that maps to Extbase
models. The Extension Builder generates this automatically.

**Required in** ``Configuration/TCA/tx_myext_domain_model_article.php``:

.. code-block:: php

   'columns' => [
       'sys_language_uid' => [
           'config' => [
               'type' => 'language',   // native type since TYPO3 12
           ],
       ],
       'crdate' => [
           'config' => [
               'type' => 'datetime',   // replaces 'input' with eval=>'datetime'
           ],
       ],
   ],

.. _migration-module-registration:

Module Registration
-------------------

**Before (TYPO3 11 — ext_tables.php):**

.. code-block:: php

   // ext_tables.php
   \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
       'MyExt',
       'web',
       'mymodule',
       '',
       [MyController::class => 'list,show'],
       ['access' => 'user,group', ...]
   );

**After (TYPO3 13 — Configuration/Backend/Modules.php):**

.. code-block:: php

   return [
       'web_myextmymodule' => [
           'parent' => 'web',
           'access' => 'user,group',
           'iconIdentifier' => 'my-ext-module',
           'labels' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang_mod.xlf',
           'extensionName' => 'MyExt',
           'controllerActions' => [
               MyController::class => ['list', 'show'],
           ],
       ],
   ];

.. _migration-di:

Dependency Injection
--------------------

All TYPO3 13 extensions must have ``Configuration/Services.yaml``:

.. code-block:: yaml

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     Vendor\MyExt\:
       resource: '../Classes/*'

This enables constructor injection for all classes under ``Classes/``.

.. _migration-checklist:

Migration Checklist
===================

Use this checklist when migrating a previously generated extension:

.. code-block:: text

   PHP / Backend
   [ ] All repository classes extend TYPO3\CMS\Extbase\Persistence\Repository
   [ ] All model properties have native PHP type declarations
   [ ] Controllers use constructor injection (not injectX() methods)
   [ ] All action methods return ResponseInterface
   [ ] declare(strict_types=1) at top of every PHP file

   TCA
   [ ] sys_language_uid uses type = 'language'
   [ ] Date/time fields use type = 'datetime'
   [ ] No deprecated type = 'input' with eval for dates

   Module / Routing
   [ ] Module registered in Configuration/Backend/Modules.php (not ext_tables.php)
   [ ] No TBE_MODULES array manipulation

   Dependency Injection
   [ ] Configuration/Services.yaml present with autowire: true
   [ ] ext_localconf.php does not use makeInstance for services
