<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Update;

use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdateUserCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private UserPasswordHasherInterface $hasher,
    ) {}

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->repository->byId($command->id);

        if (null === $user) {
            throw new \InvalidArgumentException('User not found');
        }

        if (null !== $command->password) {
            $user->setPassword($this->hasher->hashPassword($user, $command->password));
        }

        if (null !== $command->locale) {
            $user->setLocale($command->locale);
        }

        $this->repository->save($user);
    }
}
