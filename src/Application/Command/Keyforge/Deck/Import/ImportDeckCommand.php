<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class ImportDeckCommand
{
    public ?Uuid $deckId;
    public ?string $token;
    public ?Uuid $userId;

    public function __construct($deckId, $token, $userId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($userId, 'token')->nullOr()->string()->notBlank()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->verifyNow();

        $this->deckId = null === $deckId ? null : Uuid::from($deckId);
        $this->token = $token;
        $this->userId = null === $userId ? null : Uuid::from($userId);
    }
}
