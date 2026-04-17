<?php

declare(strict_types=1);

namespace Parser\Test\Model;

class ClassWithNestedMethodCalls
{
    public function countByFilter(string $searchString, array $contactPersonUids): int
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                [
                    $query->like('company', $searchString),
                    $query->in('contactPersons', $contactPersonUids),
                ]
            )
        );
        return $query->count();
    }

    public function findAllActive(): array
    {
        return $this->findBy(
            [
                'active' => true,
                'deleted' => false,
            ]
        );
    }
}
