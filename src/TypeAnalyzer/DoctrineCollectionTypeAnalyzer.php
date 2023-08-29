<?php

declare(strict_types=1);

namespace Rector\Doctrine\TypeAnalyzer;

use Doctrine\Common\Collections\Collection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;

/**
 * @todo replaces core one
 * @see \Rector\PHPStanStaticTypeMapper\DoctrineTypeAnalyzer
 */
final class DoctrineCollectionTypeAnalyzer
{
    public function detect(Type $type): bool
    {
        if (! $type instanceof UnionType) {
            return false;
        }

        $arrayType = null;
        $hasDoctrineCollectionType = false;
        foreach ($type->getTypes() as $unionedType) {
            if ($this->isCollectionObjectType($unionedType)) {
                $hasDoctrineCollectionType = true;
            }

            if ($unionedType instanceof ArrayType) {
                $arrayType = $unionedType;
            }
        }

        if (! $hasDoctrineCollectionType) {
            return false;
        }

        return $arrayType instanceof ArrayType;
    }

    private function isCollectionObjectType(Type $type): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        $className = $type instanceof ShortenedObjectType ? $type->getFullyQualifiedName() : $type->getClassName();

        return $className === Collection::class;
    }
}
