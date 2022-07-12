<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

interface UserRepository
{
    /** @return array<User> */
    public function all(): array;
    public function byId(UuidValueObject $id): ?User;
    /** @return array<User> */
    public function byIds(UuidValueObject ...$id): array;
    public function save(User $user): void;
}
