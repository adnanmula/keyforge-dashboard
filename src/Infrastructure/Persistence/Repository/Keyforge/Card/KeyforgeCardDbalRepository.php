<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Card;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\ValueObject\KeyforgeCardType;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Cards\Shared\LocalizedString;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\ParameterType;

final class KeyforgeCardDbalRepository extends DbalRepository implements KeyforgeCardRepository
{
    private const TABLE = 'keyforge_cards';

    /** @return array<KeyforgeCard> */
    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        return 0;
    }

    public function save(KeyforgeCard $card): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (id, houses, name, name_url, flavor_text, text, type, traits, amber, power, armor, is_big, is_token, sets, tags)
                VALUES (:id, :houses, :name, :name_url, :flavor_text, :text, :type, :traits, :amber, :power, :armor, :is_big, :is_token, :sets, :tags)
                ON CONFLICT (id) DO UPDATE SET
                    id = :id,
                    houses = :houses,
                    name = :name,
                    name_url = :name_url,
                    flavor_text = :flavor_text,
                    text = :text,
                    type = :type,
                    traits = :traits,
                    amber = :amber,
                    power = :power,
                    armor = :armor,
                    is_big = :is_big,
                    is_token = :is_token,
                    sets = :sets,
                    tags = :tags
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $card->id);
        $stmt->bindValue(':houses', Json::encode($card->houses));
        $stmt->bindValue(':name', Json::encode($card->name));
        $stmt->bindValue(':name_url', $card->nameUrl);
        $stmt->bindValue(':flavor_text', null === $card->flavorText ? null : Json::encode($card->flavorText));
        $stmt->bindValue(':text', Json::encode($card->text));
        $stmt->bindValue(':type', $card->type->value);
        $stmt->bindValue(':traits', Json::encode($card->traits));
        $stmt->bindValue(':amber', $card->amber);
        $stmt->bindValue(':power', $card->power);
        $stmt->bindValue(':armor', $card->armor);
        $stmt->bindValue(':is_big', $card->isBig, ParameterType::BOOLEAN);
        $stmt->bindValue(':is_token', $card->isToken, ParameterType::BOOLEAN);
        $stmt->bindValue(':sets', Json::encode($card->sets));
        $stmt->bindValue(':tags', Json::encode($card->tags));

        $stmt->executeStatement();
    }

    private function map(array $row): KeyforgeCard
    {
        return new KeyforgeCard(
            $row['id'],
            Json::decode($row['houses']),
            LocalizedString::fromArray(Json::decode($row['name'])),
            $row['name_url'],
            null === $row['flavor_text']
                ? null
                : LocalizedString::fromArray(Json::decode($row['flavor_text'])),
            LocalizedString::fromArray(Json::decode($row['text'])),
            KeyforgeCardType::from($row['type']),
            Json::decode($row['traits']),
            $row['amber'],
            $row['power'],
            $row['armor'],
            $row['is_big'],
            $row['is_token'],
            Json::decode($row['sets']),
            Json::decode($row['tags']),
        );
    }
}
