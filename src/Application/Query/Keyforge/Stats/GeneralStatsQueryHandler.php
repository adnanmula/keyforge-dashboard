<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStat;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;

final class GeneralStatsQueryHandler
{
    public function __construct(
        private KeyforgeStatRepository $repository,
    ) {}

    public function __invoke(GeneralStatsQuery $query): ?KeyforgeStat
    {
        return $this->repository->by(KeyforgeStatCategory::HOME_GENERAL_DATA, null);
    }
}
