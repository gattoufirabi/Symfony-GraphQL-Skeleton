<?php

namespace App\GraphQL\Resolver;

use App\Entity\User;
use App\Repository\UserRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Symfony\Bundle\SecurityBundle\Security;

class UserResolver implements QueryInterface, AliasedInterface
{
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(
        UserRepository $userRepository,
        Security $security
    ) {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public static function getAliases(): array
    {
        return [
            'resolveUsers' => 'user_resolver',
        ];
    }

    public function resolveUsers(User $user): Connection
    {
        return $user;
    }
}
