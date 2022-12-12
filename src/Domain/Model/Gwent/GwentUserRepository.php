<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface GwentUserRepository
{
    /** @return array<GwentUser> */
    public function all(): array;

    public function byId(Uuid $id): ?GwentUser;

    /** @return array<GwentUser> */
    public function byIds(Uuid ...$id): array;

    public function save(GwentUser $user): void;
}
