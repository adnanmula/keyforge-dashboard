<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\Import;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;
use Assert\Assert;

final class ImportKeyforgeDeckCommand
{
    private UuidValueObject $deckId;

    public function __construct($deckId)
    {
        Assert::lazy()->that($deckId, 'deckId')->uuid();

        $this->deckId = UuidValueObject::from($deckId);
    }

    public function deckId(): UuidValueObject
    {
        return $this->deckId;
    }
}
