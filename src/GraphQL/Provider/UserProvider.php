<?php

namespace App\GraphQL\Provider;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Provider]
class UserProvider
{
    #[GQL\Query(name: 'users', type: 'User', resolve: "@=query('user_connection_resolver', args)", targetTypes: ['RootDefaultQuery'])]
    #[GQL\ArgsBuilder(name: 'Relay::Connection')]
    public function getUsers()
    {
    }

}