<?php

namespace App\GraphQL\Model;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Type(name: 'Token')]
class Token
{
    #[GQL\Field(type: 'String!')]
    public string $token;

    #[GQL\Field(type: 'User!')]
    public string $auth;
}
