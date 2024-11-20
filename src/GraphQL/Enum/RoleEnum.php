<?php

namespace App\GraphQL\Enum;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Enum]
#[GQL\Description('User Role')]
class RoleEnum
{
    public const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
    public const ROLE_USER = 'ROLE_USER';

    public $value;
}
