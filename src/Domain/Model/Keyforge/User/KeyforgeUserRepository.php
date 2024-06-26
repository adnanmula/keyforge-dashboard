<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\User;

use AdnanMula\Criteria\Criteria;

interface KeyforgeUserRepository
{
    /** @return array<KeyforgeUser> */
    public function search(Criteria $criteria): array;

    public function save(KeyforgeUser $user): void;
}
