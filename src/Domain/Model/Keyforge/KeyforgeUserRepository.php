<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeUserRepository
{
    /** @return array<KeyforgeUser> */
    public function all(bool $withExternal): array;

    public function byId(Uuid $id): ?KeyforgeUser;

    public function byName(string $name): ?KeyforgeUser;

    /** @return array<KeyforgeUser> */
    public function byIds(Uuid ...$id): array;

    public function save(KeyforgeUser $user): void;
}
