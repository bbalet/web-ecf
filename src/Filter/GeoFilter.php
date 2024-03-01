<?php
// api/src/Filter/RegexpFilter.php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class GeoFilter extends AbstractFilter
{
    /**
     * Not used as the logic is implemented in the custom state provider TheaterStateProvider
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
        $description["latitude"] = [
            'property' => 'latitude',
            'type' => Type::BUILTIN_TYPE_FLOAT,
            'required' => false,
            'description' => 'latitude of the current position',
            'openapi' => [
                'example' => '43.5654431',
                'allowReserved' => true,
                'allowEmptyValue' => true,
                'explode' => false,
            ],
        ];
        $description["longitude"] = [
            'property' => 'longitude',
            'type' => Type::BUILTIN_TYPE_FLOAT,
            'required' => false,
            'description' => 'longitude of the current position',
            'openapi' => [
                'example' => '1.3838146',
                'allowReserved' => true,
                'allowEmptyValue' => true,
                'explode' => false,
            ],
        ];
        return $description;
    }
}
