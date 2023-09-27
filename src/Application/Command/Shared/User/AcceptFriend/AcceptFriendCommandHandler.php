<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\AcceptFriend;

use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;

final class AcceptFriendCommandHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(AcceptFriendCommand $command): void
    {
        $user = $this->repository->byId($command->user);
        $friend = $this->repository->byId($command->friendId);

        if (null === $user || null === $friend) {
            throw new UserNotExistsException();
        }

        $request = $this->repository->friendRequest($friend->id(), $command->user);

        if (null === $request) {
            return;
        }

        $this->repository->addFriend($user->id(), $friend->id(), false);
        $this->repository->addFriend($friend->id(), $user->id(), false);
    }
}
