<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetDecksTagsQuery
{
    private(set) Uuid $userId;
    private(set) array $deckIds;

    public function __construct($userId, $deckIds)
    {
        Assert::lazy()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->that($deckIds, 'deckIds')->isArray()->minCount(1)->all()->uuid()
            ->verifyNow();

        $this->userId = Uuid::from($userId);
        $this->deckIds = $deckIds;
    }
}
