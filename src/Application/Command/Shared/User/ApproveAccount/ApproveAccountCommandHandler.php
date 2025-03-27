<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount;

use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

final readonly class ApproveAccountCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private KeyforgeUserRepository $kfUserRepository,
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

        if ($command->approve) {
            $user->setRole(UserRole::ROLE_KEYFORGE);

            $this->kfUserRepository->save(KeyforgeUser::create($user->id(), $user->name(), null));
        } else {
            $user->setRole(UserRole::ROLE_REJECTED_ACCOUNT);
        }

        $this->repository->save($user);
    }
}
