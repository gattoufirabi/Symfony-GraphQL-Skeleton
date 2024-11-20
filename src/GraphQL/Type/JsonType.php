<?php

/**
 * PHP version 7.4 & Symfony 4.4.
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * https://www.php.net/license/3_01.txt.
 *
 * Keytchens developed by Ben Macha.
 *
 * @category   Symfony Project
 *
 * @author     Ali BEN MECHA       <dali@keytchens.com>
 * @dev        Abdessalem GHOZIA   <ghozia@keytchens.com>
 * @dev        Abdallah LAHBIB     <abdallah@keytchens.com>
 * @dev        Rabï Gattoufi       <rabi@keytchens.com>
 *
 * @copyright  Ⓒ 2018 Cubes.TN
 *
 * @see       https://www.benmacha.tn
 * @see       https://keytchens.com
 * @see       https://keytchens.app
 *
 */

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
