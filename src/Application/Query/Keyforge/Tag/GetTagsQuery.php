<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use Assert\Assert;

final class GetTagsQuery
{
    private ?TagVisibility $visibility;

    public function __construct($visibility)
    {
        Assert::lazy()
            ->that($visibility, 'visibility')->nullOr()->inArray(TagVisibility::allowedValues())
            ->verifyNow();

        $this->visibility = null === $visibility
            ? null
            : TagVisibility::from($visibility);
    }

    public function visibility(): ?TagVisibility
    {
        return $this->visibility;
    }
}
