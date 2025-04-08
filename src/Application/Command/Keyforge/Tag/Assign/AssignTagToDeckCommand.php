<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Assign;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class AssignTagToDeckCommand
{
    private(set) Uuid $deckId;
    private(set) array $tagIds;

    public function __construct($deckId, $tagIds)
    {
        Assert::lazy()
            ->that($deckId, 'deckId')->uuid()
            ->that($tagIds, 'tagIds')->all()->uuid()
            ->verifyNow();

        $this->deckId = Uuid::from($deckId);
        $this->tagIds = $tagIds;
    }
}
