<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class GetTagsQuery
{
    private ?array $ids;
    private ?TagVisibility $visibility;

    public function __construct($ids, $visibility)
    {
        Assert::lazy()
            ->that($ids, 'ids')->nullOr()->all()->uuid()
            ->that($visibility, 'visibility')->nullOr()->inArray(TagVisibility::allowedValues())
            ->verifyNow();

        $this->ids = null === $ids
            ? null
            : \array_map(static fn (string $id): Uuid => Uuid::from($id), $ids);

        $this->visibility = null === $visibility
            ? null
            : TagVisibility::from($visibility);
    }

    public function ids(): ?array
    {
        return $this->ids;
    }

    public function visibility(): ?TagVisibility
    {
        return $this->visibility;
    }
}
