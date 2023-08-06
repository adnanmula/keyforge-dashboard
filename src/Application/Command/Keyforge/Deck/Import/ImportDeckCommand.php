<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class ImportDeckCommand
{
    public Uuid $deckId;
    public ?Uuid $userId;

    public function __construct($deckId, $userId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
        $this->userId = null === $userId ? null : Uuid::from($userId);
    }
}
