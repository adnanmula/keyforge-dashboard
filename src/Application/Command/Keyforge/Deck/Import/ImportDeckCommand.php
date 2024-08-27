<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class ImportDeckCommand
{
    public ?Uuid $deckId;
    public KeyforgeDeckType $deckType;
    public ?string $token;
    public ?Uuid $userId;

    public function __construct($deckId, $deckType, $token, $userId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($deckType, 'deckType')->inArray(KeyforgeDeckType::values())
            ->that($userId, 'token')->nullOr()->string()->notBlank()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->verifyNow();

        $this->deckId = null === $deckId ? null : Uuid::from($deckId);
        $this->deckType = KeyforgeDeckType::from($deckType);
        $this->token = $token;
        $this->userId = null === $userId ? null : Uuid::from($userId);
    }
}
