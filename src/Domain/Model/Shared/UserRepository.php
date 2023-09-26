<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface UserRepository
{
    public function byId(Uuid $id): ?User;
    public function byName(string $name): ?User;
    public function save(User $user): void;
    public function friends(Uuid $id): array;
    public function addFriend(Uuid $id, Uuid $friend): void;
    public function removeFriend(Uuid $id, Uuid $friendId): void;
}
