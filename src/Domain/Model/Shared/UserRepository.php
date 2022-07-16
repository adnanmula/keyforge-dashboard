<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

interface UserRepository
{
    public function byName(string $name): ?User;

    public function save(User $user): void;
}
