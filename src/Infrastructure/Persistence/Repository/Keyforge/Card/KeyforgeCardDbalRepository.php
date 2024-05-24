<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Card;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use Doctrine\DBAL\ParameterType;

final class KeyforgeCardDbalRepository extends DbalRepository implements KeyforgeCardRepository
{
    private const TABLE = 'keyforge_cards';

    /** @return array<KeyforgeCard> */
    public function search(Criteria $criteria): array
    {
        return [];
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
                INSERT INTO %s (
                    id,
                    houses,
                    name,
                    name_url,
                    flavor_text,
                    text,
                    type,
                    traits,
                    amber,
                    power,
                    armor,
                    is_big,
                    is_token,
                    sets,
                    tags,
                    raw_data
                ) VALUES (
                    :id,
                    :houses,
                    :name,
                    :name_url,
                    :flavor_text,
                    :text,
                    :type,
                    :traits,
                    :amber,
                    :power,
                    :armor,
                    :is_big,
                    :is_token,
                    :sets,
                    :tags,
                    :raw_data
                ) ON CONFLICT (id) DO UPDATE SET
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
                    tags = :tags,
                    raw_data = :raw_data
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
        $stmt->bindValue(':raw_data', Json::encode($card->rawData));

        $stmt->executeStatement();
    }
}
