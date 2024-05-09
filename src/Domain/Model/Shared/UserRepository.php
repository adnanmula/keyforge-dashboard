<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface UserRepository
{
    public function save(User $user): void;
    public function byId(Uuid $id): ?User;
    public function byName(string $name): ?User;
    public function byRole(UserRole $role): array;
    public function friends(Uuid $id): array;
    public function friendRequest(Uuid $user, Uuid $friend): ?array;
    public function addFriend(Uuid $id, Uuid $friend, bool $isRequest): void;
    public function removeFriend(Uuid $id, Uuid $friendId): void;
}
