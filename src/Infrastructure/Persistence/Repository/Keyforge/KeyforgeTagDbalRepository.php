<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\DbalCriteriaAdapter;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class KeyforgeTagDbalRepository extends DbalRepository implements KeyforgeTagRepository
{
    private const TABLE = 'keyforge_tags';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.name, a.visibility, a.style')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->execute()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function save(KeyforgeTag $tag): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, visibility, style)
                    VALUES (:id, :name, :visibility, :style)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        visibility = :visibility,
                        style = :style
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $tag->id->value());
        $stmt->bindValue(':name', $tag->name());
        $stmt->bindValue(':visibility', $tag->visibility()->name);
        $stmt->bindValue(':style', Json::encode($tag->style()));

        $stmt->execute();
    }

    private function map(array $tag): KeyforgeTag
    {
        return new KeyforgeTag(
            Uuid::from($tag['id']),
            $tag['name'],
            TagVisibility::from($tag['visibility']),
            TagStyle::from(Json::decode($tag['style'])),
        );
    }
}