<?php

namespace App\GraphQL\Provider;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Type]
class RootDefaultQuery
{
    #[GQL\Query(type: 'String')]
    public function helloWorld(string $name): string
    {
        return 'Hello ' . $name;
    }
}
