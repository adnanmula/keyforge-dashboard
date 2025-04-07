<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Assign;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class AssignTagToDeckCommand
{
    private(set) Uuid $deckId;
    private(set) Uuid $tagId;

    public function __construct($deckId, $tagId)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->that($tagId, 'tagId')->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
        $this->tagId = Uuid::from($tagId);
    }
}
