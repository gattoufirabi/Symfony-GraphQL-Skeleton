<?php

namespace App\GraphQL\Provider;

use App\Entity\User;
use App\Exception\GraphQLException;
use App\Form\CreateUserType;
use App\Form\LoginType;
use App\Form\Request\CreateUserRequest;
use App\Form\Request\LoginRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[GQL\Provider]
class SecurityProvider
{
    public function __construct(
        private readonly FormFactoryInterface        $formFactory,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface    $JWTTokenManager,
    ){
    }
    #[GQL\Mutation(name: 'login', type: 'Token', targetTypes: ['RootDefaultMutation'])]
    #[GQL\Arg(name: 'email', type: 'String!', description: 'The user email linked to his profile.')]
    #[GQL\Arg(name: 'password', type: 'String!', description: 'The user password linked to his email.')]
    public function login(string $email, string $password): array
    {
        $request = new LoginRequest();
        $form = $this->formFactory->create(LoginType::class, $request);

        $form->submit(['email' => $email, 'password' => $password]);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        $auth = $this->userRepository->loadUserByUsername($request->getEmail());

        if (!$auth) {
            throw GraphQLException::fromString('security.password.incorrect');
        }

        if (!$auth->getIsEnabled()) {
            throw GraphQLException::fromString('security.user.disabled');
        }

        if ($this->passwordHasher->isPasswordValid($auth, $request->getPassword())) {
            $token = $this->JWTTokenManager->create($auth);

            return [
                'token' => $token,
                'auth' => $auth,
            ];
        }

        throw GraphQLException::fromString('security.password.incorrect');
    }
    #[GQL\Mutation(name: 'createUser', type: 'User', targetTypes: ['RootDefaultMutation'])]
    #[GQL\Arg(name: 'email', type: 'String!', description: 'The email of the user to create.')]
    #[GQL\Arg(name: 'password', type: 'String!', description: 'The password of the user to create.')]
    #[GQL\Arg(name: 'name', type: 'String!', description: 'The full name of the user.')]
    public function createUser(string $email, string $password, string $name): User
    {
        $request = new CreateUserRequest();
        $form = $this->formFactory->create(CreateUserType::class, $request);

        $form->submit([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ]);

        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        $user = new User();
        $user->setUuid(Uuid::v4());
        $user->setEmail($request->getEmail());
        $user->setName($request->getName());
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $request->getPassword())
        );
        $user->setIsEnabled(true);

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }
}