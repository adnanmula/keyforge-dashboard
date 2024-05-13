<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount;

use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

final readonly class ApproveAccountCommandHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(ApproveAccountCommand $command): void
    {
        $user = $this->repository->byId($command->user);

        if (null === $user) {
            throw new UserNotExistsException();
        }

        if ($user->getRoles() !== [UserRole::ROLE_BASIC->value]) {
            throw new UnsupportedUserException();
        }

        $user->setRole(UserRole::ROLE_KEYFORGE);

        $this->repository->save($user);
    }
}
