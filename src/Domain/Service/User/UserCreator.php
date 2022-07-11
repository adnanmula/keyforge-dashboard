<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\User;

use AdnanMula\Cards\Domain\Model\User\Exception\UserAlreadyExistsException;
use AdnanMula\Cards\Domain\Model\User\User;
use AdnanMula\Cards\Domain\Model\User\UserRepository;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserId;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserUsername;

final class UserCreator
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserId $id, UserReference $reference, UserUsername $username): void
    {
        $user = $this->repository->byReference($reference);

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        $this->repository->save(
            User::create($id, $reference, $username),
        );
    }
}
