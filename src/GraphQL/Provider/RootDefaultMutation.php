<?php


namespace App\GraphQL\Provider;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Type]
class RootDefaultMutation
{
    #[GQL\Mutation(type: 'String')]
    public function helloWorld(string $name): string
    {
        return 'Hello ' . $name;
    }
}
