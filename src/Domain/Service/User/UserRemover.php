<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\User;

use AdnanMula\Cards\Domain\Model\User\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\User\UserRepository;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;

final class UserRemover
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserReference $reference): void
    {
        $user = $this->repository->byReference($reference);

        if (null === $user) {
            throw new UserNotExistsException();
        }

        $this->repository->remove($user);
    }
}
