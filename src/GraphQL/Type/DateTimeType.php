<?php

namespace App\GraphQL\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Language\AST\Node;
use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Scalar(name: 'DateTimeType')]
class DateTimeType
{
    public static function serialize($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format(DateTimeInterface::ATOM);
        }
        if ($value instanceof DateTimeImmutable) {
            return $value->format(DateTimeInterface::ATOM);
        }

        return $value;
    }

    public static function parseValue($value): DateTime
    {
        return new DateTime($value);
    }
    public static function parseLiteral(Node $valueNode): DateTime
    {
        return new DateTime($valueNode->value);
    }
}
