<?php

namespace App\GraphQL\Type;

use GraphQL\Language\AST\Node;
use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Scalar(name: 'JsonType')]
class JsonType
{
    public static function serialize(array $value): array|string
    {
        return $value;
    }

    public static function parseValue($value)
    {
        return json_decode($value, true);
    }
    public static function parseLiteral(Node $valueNode): string
    {
        return json_decode($valueNode, true);
    }
}
