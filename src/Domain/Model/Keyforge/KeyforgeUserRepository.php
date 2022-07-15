<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeUserRepository
{
    /** @return array<KeyforgeUser> */
    public function all(): array;

    public function byId(Uuid $id): ?KeyforgeUser;

    /** @return array<KeyforgeUser> */
    public function byIds(Uuid ...$id): array;

    public function save(KeyforgeUser $user): void;
}
