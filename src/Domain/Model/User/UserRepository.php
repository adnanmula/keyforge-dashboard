<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface UserRepository
{
    /** @return array<User> */
    public function all(): array;
    public function byId(Uuid $id): ?User;
    /** @return array<User> */
    public function byIds(Uuid ...$id): array;
    public function save(User $user): void;
}
