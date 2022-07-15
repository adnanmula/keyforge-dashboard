<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class ImportDeckCommand
{
    private Uuid $deckId;

    public function __construct($deckId)
    {
        Assert::lazy()->that($deckId, 'deckId')->uuid();

        $this->deckId = Uuid::from($deckId);
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }
}
