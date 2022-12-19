<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class ImportDeckCommand
{
    private Uuid $deckId;
    private Uuid $userId;

    public function __construct($deckId, $userId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->that($userId, 'userId')->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
        $this->userId = Uuid::from($userId);
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }
}
