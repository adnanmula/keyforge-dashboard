<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class GetGamesByDeckQuery
{
    private Uuid $deckId;

    public function __construct($deckId)
    {
        Assert::lazy()->that($deckId, 'deck_id')->uuid();

        $this->deckId = Uuid::from($deckId);
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }
}
