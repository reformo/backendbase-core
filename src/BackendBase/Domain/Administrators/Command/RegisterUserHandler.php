<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

use BackendBase\Domain\Administrators\Exception\UserAlreadyExists;
use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Interfaces\UserRepository;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserByEmail;
use BackendBase\Domain\Administrators\Persistence\Doctrine\UserGenerator;
use BackendBase\Shared\CQRS\Interfaces\CommandHandler;

class RegisterUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private GetUserByEmail $getUserByEmailQuery,
    ) {
    }

    public function __invoke(RegisterUser $message): void
    {
        $userExists = false;
        try {
            $this->getUserByEmailQuery->query(['email' => $message->email()]);
            $userExists = true;
        } catch (UserNotFound) {
        }

        if ($userExists === true) {
            throw UserAlreadyExists::create('User with this email address exists');
        }

        $user = UserGenerator::fromCommand($message);
        $this->repository->registerUser($user);
    }
}
