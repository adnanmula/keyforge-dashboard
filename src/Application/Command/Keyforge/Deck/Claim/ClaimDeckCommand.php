<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Claim;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class ClaimDeckCommand
{
    private Uuid $userId;
    private Uuid $deckId;

    public function __construct($userId, $deckId)
    {
        Assert::lazy()
            ->that($userId, 'userId')->uuid()
            ->that($deckId, 'deckId')->uuid()
            ->verifyNow();

        $this->userId = Uuid::from($userId);
        $this->deckId = Uuid::from($deckId);
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }
}
