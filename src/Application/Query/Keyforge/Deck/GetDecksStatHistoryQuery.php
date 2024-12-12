<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetDecksStatHistoryQuery
{
    private(set) array $ids;

    public function __construct(string ...$ids)
    {
        Assert::lazy()
            ->that($ids, 'ids')->all()->uuid()
            ->verifyNow();

        $this->ids = array_map(static fn (string $id) => Uuid::from($id), $ids);
    }
}
