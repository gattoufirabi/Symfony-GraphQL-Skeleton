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

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserAPIProvider implements UserProviderInterface
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }
    public function loadUserByIdentifier(mixed $identifier): UserInterface
    {
        return $this->userRepository->findOneBy(['email' => $identifier]) ?? $this->userRepository->findOneBy(['uuid' => $identifier]);
    }
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
