<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;

final readonly class GetTagsQueryHandler
{
    public function __construct(
        private KeyforgeTagRepository $repository,
    ) {}

    public function __invoke(GetTagsQuery $query): array
    {
        $tags = $this->repository->search($query->criteria);

        return [
            'tags' => $tags,
        ];
    }
}
