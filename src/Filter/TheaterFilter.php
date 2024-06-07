<?php
// api/src/Filter/RegexpFilter.php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class TheaterFilter extends AbstractFilter
{
    /**
     * Not used as the logic is implemented in the custom state provider IssueStateProvider
     *
     * @param string $property
     * @param [type] $value
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }
        $description = [];
        $description["theaterId"] = [
            'property' => 'theaterId',
            'type' => Type::BUILTIN_TYPE_INT,
            'required' => false,
            'description' => 'Theater identifier',
            'openapi' => [
                'example' => '133',
                'allowReserved' => true,
                'allowEmptyValue' => false,
                'explode' => false,
            ],
        ];
        return $description;
    }
}
