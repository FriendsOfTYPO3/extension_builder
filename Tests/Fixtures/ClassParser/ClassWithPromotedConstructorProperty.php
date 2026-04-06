<?php

declare(strict_types=1);

namespace Fixtures\ClassParser;

use Fixtures\ClassParser\SomeRepository;

class ClassWithPromotedConstructorProperty
{
    public function __construct(
        private readonly SomeRepository $someRepository,
    ) {}
}
