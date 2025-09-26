<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;

final readonly class GetGameLogQueryHandler
{
    public function __construct(
        private KeyforgeGameRepository $repository,
    ) {}

    public function __invoke(GetGameLogQuery $query): ?KeyforgeGameLog
    {
        return $this->repository->gameLog($query->id);
    }
}
