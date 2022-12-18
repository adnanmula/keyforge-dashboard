<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Create;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private UserPasswordHasherInterface $hasher,
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $user = $this->repository->byName($command->name());

        if (null !== $user) {
            throw new \InvalidArgumentException('User already exists');
        }

        $newUser = new User(
            Uuid::v4(),
            $command->name(),
            '',
            ['ROLE_BASIC'],
        );

        $hashedPassword = $this->hasher->hashPassword(
            $newUser,
            $command->password(),
        );

        $newUser->setPassword($hashedPassword);

        $this->repository->save($newUser);
    }
}
