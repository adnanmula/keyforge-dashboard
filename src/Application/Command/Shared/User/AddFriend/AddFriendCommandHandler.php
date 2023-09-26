<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\AddFriend;

use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;

final class AddFriendCommandHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(AddFriendCommand $command): void
    {
        $user = $this->repository->byId($command->user);

        if ($user->name() === $command->friendName) {
            return;
        }

        $friend = $this->repository->byName($command->friendName);

        if (null === $user || null === $friend) {
            throw new UserNotExistsException();
        }

        $this->repository->addFriend($user->id(), $friend->id());
        $this->repository->addFriend($friend->id(), $user->id());
    }
}
