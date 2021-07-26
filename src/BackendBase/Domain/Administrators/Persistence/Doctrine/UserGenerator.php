<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine;

use BackendBase\Domain\Administrators\Command\RegisterUser;
use BackendBase\Domain\Administrators\Model\User;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\User as DoctrineUser;
use Carbon\CarbonImmutable;
use DateTimeImmutable;

class UserGenerator
{
    public static function fromCommand(RegisterUser $message): User
    {
        return User::new(
            $message->id(),
            $message->email(),
            $message->firstName(),
            $message->lastName(),
            $message->passwordHash(),
            $message->role(),
            new DateTimeImmutable()
        );
    }

    public static function fromArray(array $userData): User
    {
        return User::create(
            $userData['id'],
            $userData['email'],
            $userData['firstName'],
            $userData['lastName'],
            $userData['passwordHash'],
            $userData['role'],
            (int) $userData['isActive'],
            (new CarbonImmutable($userData['createdAt']))
                ->toDateTimeImmutable()
        );
    }

    public static function toDoctrineEntity(User $user): DoctrineUser
    {
        $doctrineEntity = new DoctrineUser();
        $doctrineEntity->setId($user->id()->toString());
        $doctrineEntity->setEmail($user->email()->toString());
        $doctrineEntity->setPasswordHash($user->passwordHash());
        $doctrineEntity->setFirstName($user->firstName());
        $doctrineEntity->setLastName($user->lastName());
        $doctrineEntity->setRole($user->role());
        $doctrineEntity->setIsActive((int) $user->isActive());
        $doctrineEntity->setCreatedAt($user->createdAt());

        return $doctrineEntity;
    }
}
