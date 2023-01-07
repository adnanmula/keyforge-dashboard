<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Analyze;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class AnalyzeDeckThreatsCommand
{
    public readonly Uuid $deckId;

    public function __construct($deckId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
    }
}
