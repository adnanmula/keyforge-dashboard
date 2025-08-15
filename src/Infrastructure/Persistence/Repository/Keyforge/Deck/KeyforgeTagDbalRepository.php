<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Cards\Shared\LocalizedString;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\ParameterType;

final class KeyforgeTagDbalRepository extends DbalRepository implements KeyforgeTagRepository
{
    private const string TABLE = 'keyforge_tags';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn(array $row) => $this->map($row), $result);
    }

    public function searchOne(Criteria $criteria): ?KeyforgeDeckTag
    {
        $criteria = new Criteria(
            $criteria->filters(),
            $criteria->offset(),
            1,
            $criteria->sorting(),
        );

        $result = $this->search($criteria);

        return $result[0] ?? null;
    }

    public function save(KeyforgeDeckTag $tag): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, user_id, name, visibility, style, type, archived)
                    VALUES (:id, :user_id, :name, :visibility, :style, :type, :archived)
                    ON CONFLICT (id) DO UPDATE SET
                        user_id = :user_id,
                        name = :name,
                        visibility = :visibility,
                        style = :style,
                        type = :type,
                        archived = :archived
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $tag->id->value());
        $stmt->bindValue(':user_id', $tag->userId?->value());
        $stmt->bindValue(':name', Json::encode($tag->name));
        $stmt->bindValue(':visibility', $tag->visibility->name);
        $stmt->bindValue(':style', Json::encode($tag->style));
        $stmt->bindValue(':type', $tag->type->name);
        $stmt->bindValue(':archived', $tag->archived, ParameterType::BOOLEAN);

        $stmt->executeStatement();
    }

    public function remove(Uuid $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->executeStatement();
    }

    private function map(array $tag): KeyforgeDeckTag
    {
        return new KeyforgeDeckTag(
            Uuid::from($tag['id']),
            LocalizedString::fromArray(Json::decode($tag['name'])),
            TagVisibility::from($tag['visibility']),
            TagStyle::from(Json::decode($tag['style'])),
            TagType::from($tag['type']),
            $tag['archived'],
            Uuid::fromNullable($tag['user_id']),
        );
    }
}
