<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeUserRepository
{
    /** @return array<KeyforgeUser> */
    public function search(Criteria $criteria): array;

    public function searchOne(Criteria $criteria): ?KeyforgeUser;

    public function save(KeyforgeUser $user): void;

    public function winrate(Uuid $id): array;

    public function bestDecks(Uuid $id): array;
}
