<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetTagsQuery
{
    private(set) ?array $ids;
    private(set) ?TagVisibility $visibility;
    private(set) ?bool $archived;

    public function __construct($ids, $visibility, $archived)
    {
        Assert::lazy()
            ->that($ids, 'ids')->nullOr()->all()->uuid()
            ->that($visibility, 'visibility')->nullOr()->inArray(TagVisibility::values())
            ->that($archived, 'archived')->nullOr()->boolean()
            ->verifyNow();

        $this->ids = null === $ids
            ? null
            : \array_map(static fn (string $id): Uuid => Uuid::from($id), $ids);

        $this->visibility = null === $visibility
            ? null
            : TagVisibility::from($visibility);

        $this->archived = $archived;
    }
}
