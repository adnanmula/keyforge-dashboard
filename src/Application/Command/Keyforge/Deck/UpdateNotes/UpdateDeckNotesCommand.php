<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class UpdateDeckNotesCommand
{
    public readonly Uuid $deckId;
    public readonly string $notes;
    public readonly Uuid $userId;

    public function __construct($deckId, $notes, $userId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->that($notes, 'notes')->string()->maxLength(512)
            ->that($userId, 'userId')->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
        $this->notes = $notes;
        $this->userId = Uuid::from($userId);
    }
}
