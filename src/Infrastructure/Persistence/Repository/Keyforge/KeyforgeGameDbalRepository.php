<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerm;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\SearchTermType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Query\QueryBuilder;

final class KeyforgeGameDbalRepository extends DbalRepository implements KeyforgeGameRepository
{
    private const TABLE = 'keyforge_games';

    public function search(?SearchTerms $search, ?Pagination $pagination, ?QueryOrder $order): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a');

        if (null !== $search) {
            $query = $this->applySearch($search, $builder, $query);
        }

        if (null !== $pagination) {
            $query->setFirstResult($pagination->start())->setMaxResults($pagination->length());
        }

        if (null === $order) {
            $query->orderBy('a.date', 'DESC')
                ->addOrderBy('a.created_at', 'DESC');
        } else {
            $query->orderBy($order->field(), $order->order());

            if ($order->field() === 'date') {
                if ($order->order() === 'asc') {
                    $query->addOrderBy('a.created_at', 'ASC');
                } else {
                    $query->addOrderBy('a.created_at', 'DESC');
                }
            }
        }

        $result = $query->execute()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function all(?Pagination $pagination): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC');

        if (null !== $pagination) {
            $query->setFirstResult($pagination->start())->setMaxResults($pagination->length());
        }

        $result = $query->execute()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function count(?SearchTerms $search = null): int
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('count(a.*)')
            ->from(self::TABLE, 'a');

        if (null !== $search) {
            $this->applySearch($search, $builder, $query);
        }

        return $query->execute()->fetchOne();
    }

    public function save(KeyforgeGame $game): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date, created_at, winner_chains, loser_chains)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date, :created_at, :winner_chains, :loser_chains)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        winner = :winner,
                        loser = :loser,
                        winner_deck = :winner_deck,
                        loser_deck = :loser_deck,
                        first_turn = :first_turn,
                        score = :score,
                        date = :date,
                        created_at = :created_at,
                        winner_chains = :winner_chains,
                        loser_chains = :loser_chains
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $game->id()->value());
        $stmt->bindValue(':winner', $game->winner()->value());
        $stmt->bindValue(':loser', $game->loser()->value());
        $stmt->bindValue(':winner_deck', $game->winnerDeck()->value());
        $stmt->bindValue(':loser_deck', $game->loserDeck()->value());
        $stmt->bindValue(':winner_chains', $game->winnerChains());
        $stmt->bindValue(':loser_chains', $game->loserChains());
        $stmt->bindValue(':first_turn', $game->firstTurn()?->value());
        $stmt->bindValue(':score', Json::encode($game->score()));
        $stmt->bindValue(':date', $game->date()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':created_at', $game->createdAt()->format(\DateTimeInterface::ATOM));

        $stmt->execute();
    }

    private function map(array $game): KeyforgeGame
    {
        $score = Json::decode($game['score']);

        return new KeyforgeGame(
            Uuid::from($game['id']),
            Uuid::from($game['winner']),
            Uuid::from($game['loser']),
            Uuid::from($game['winner_deck']),
            Uuid::from($game['loser_deck']),
            $game['winner_chains'],
            $game['loser_chains'],
            null === $game['first_turn'] ? null : Uuid::from($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
            new \DateTimeImmutable($game['created_at']),
        );
    }

    private function applySearch(SearchTerms $terms, QueryBuilder $builder, QueryBuilder $query): QueryBuilder
    {
        /** @var SearchTerm $term */
        foreach ($terms as $term) {
            $firstFilter = \sprintf('%s = :%s', $term->filters()[0]->field(), $term->filters()[0]->field() . '0');

            if ($term->type() === SearchTermType::OR) {
                $expression = $builder->expr()->or($firstFilter);
            } else {
                $expression = $builder->expr()->and($firstFilter);
            }

            foreach ($term->filters() as $index => $filter) {
                if ($index === 0) {
                    continue;
                }

                $expression = $expression->with(\sprintf('%s = :%s', $filter->field(), $filter->field() . $index));
            }

            if ($terms->type() === SearchTermType::AND) {
                $query->andWhere($expression);
            }

            if ($terms->type() === SearchTermType::OR) {
                $query->orWhere($expression);
            }

            foreach ($term->filters() as $index => $filter) {
                $query->setParameter($filter->field() . $index, $filter->value());
            }
        }

        return $query;
    }
}
