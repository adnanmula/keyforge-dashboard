<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeUserRepository
{
    /** @return array<KeyforgeUser> */
    public function search(Criteria $criteria): array;

    public function byId(Uuid $id): ?KeyforgeUser;

    public function byName(string $name): ?KeyforgeUser;

    /** @return array<KeyforgeUser> */
    public function byIds(Uuid ...$id): array;

    public function save(KeyforgeUser $user): void;
}
