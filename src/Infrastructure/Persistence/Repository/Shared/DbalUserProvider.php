<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Shared;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<User>
 */
final class DbalUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (false === $user instanceof User) {
            throw new UnsupportedUserException(
                \sprintf('Instances of "%s" are not supported.', $user::class),
            );
        }

        $email = $user->getUserIdentifier();

        return $this->loadUserByIdentifier($email);
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->repository->byName($identifier);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
