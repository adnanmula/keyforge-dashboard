<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\FriendRemove;

use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;

final class RemoveFriendCommandHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(RemoveFriendCommand $command): void
    {
        $user = $this->repository->byId($command->user);
        $friend = $this->repository->byId($command->friendId);

        if (null === $user || null === $friend) {
            throw new UserNotExistsException();
        }

        $this->repository->removeFriend($user->id(), $friend->id());
        $this->repository->removeFriend($friend->id(), $user->id());
    }
}
