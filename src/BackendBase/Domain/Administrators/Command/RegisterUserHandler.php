<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

use BackendBase\Domain\Administrators\Exception\UserAlreadyExists;
use BackendBase\Domain\Administrators\Interfaces\UserRepository;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserByEmail;
use BackendBase\Domain\Administrators\Persistence\Doctrine\UserMapper;
use BackendBase\Shared\CQRS\Interfaces\CommandHandler;
use Throwable;

class RegisterUserHandler implements CommandHandler
{
    private UserRepository $repository;
    private GetUserByEmail $getUserByEmailQuery;

    public function __construct(UserRepository $repository, GetUserByEmail $getUserByEmailQuery)
    {
        $this->repository          = $repository;
        $this->getUserByEmailQuery = $getUserByEmailQuery;
    }

    public function __invoke(RegisterUser $message): void
    {
        $userExists = false;
        try {
            $this->getUserByEmailQuery->query(['email' => $message->email()]);
            $userExists = true;
        } catch (Throwable $exception) {
        }

        if ($userExists === true) {
            throw UserAlreadyExists::create('User with this email exists');
        }

        $user = UserMapper::fromCommandToEntity($message);
        $this->repository->registerUser($user);
    }
}
