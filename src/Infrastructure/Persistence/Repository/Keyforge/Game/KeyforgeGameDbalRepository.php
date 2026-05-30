<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Game;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use AdnanMula\Criteria\FilterField\FieldMapping;
use Doctrine\DBAL\ParameterType;

final class KeyforgeGameDbalRepository extends DbalRepository implements KeyforgeGameRepository
{
    private const string TABLE = 'keyforge_games';
    private const string TABLE_GAME_LOG = 'keyforge_game_logs';

    private const array fieldMapping = [
        'id' => 'a.id',
        'log_id' => 'b.id',
        'turns' => 'b.turns',
        'winner_amber_obtained' => 'b.winner_amber_obtained',
        'winner_amber_stolen' => 'b.winner_amber_stolen',
        'winner_cards_played' => 'b.winner_cards_played',
        'winner_cards_drawn' => 'b.winner_cards_drawn',
        'winner_cards_discarded' => 'b.winner_cards_discarded',
        'winner_keys_forged' => 'b.winner_keys_forged',
        'winner_fights' => 'b.winner_fights',
        'winner_reaps' => 'b.winner_reaps',
        'winner_extra_turns' => 'b.winner_extra_turns',
        'loser_amber_obtained' => 'b.loser_amber_obtained',
        'loser_amber_stolen' => 'b.loser_amber_stolen',
        'loser_cards_played' => 'b.loser_cards_played',
        'loser_cards_drawn' => 'b.loser_cards_drawn',
        'loser_cards_discarded' => 'b.loser_cards_discarded',
        'loser_keys_forged' => 'b.loser_keys_forged',
        'loser_fights' => 'b.loser_fights',
        'loser_reaps' => 'b.loser_reaps',
        'loser_extra_turns' => 'b.loser_extra_turns',
        'total_amber_obtained' => 'b.total_amber_obtained',
        'total_amber_stolen' => 'b.total_amber_stolen',
        'total_cards_played' => 'b.total_cards_played',
        'total_cards_drawn' => 'b.total_cards_drawn',
        'total_cards_discarded' => 'b.total_cards_discarded',
        'total_keys_forged' => 'b.total_keys_forged',
        'total_fights' => 'b.total_fights',
        'total_reaps' => 'b.total_reaps',
        'total_extra_turns' => 'b.total_extra_turns',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select(
            'a.*, b.log, b.id as log_id,
            b.turns, b.winner_amber_obtained, b.winner_amber_stolen, b.winner_cards_played, b.winner_cards_drawn, b.winner_cards_discarded, b.winner_keys_forged, b.winner_fights, b.winner_reaps, b.winner_extra_turns,
            b.loser_amber_obtained, b.loser_amber_stolen, b.loser_cards_played, b.loser_cards_drawn, b.loser_cards_discarded, b.loser_keys_forged, b.loser_fights, b.loser_reaps, b.loser_extra_turns,
            b.total_amber_obtained, b.total_amber_stolen, b.total_cards_played, b.total_cards_drawn, b.total_cards_discarded, b.total_keys_forged, b.total_fights, b.total_reaps, b.total_extra_turns'
        )
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_GAME_LOG, 'b', 'a.id = b.game_id');

        new DbalCriteriaAdapter($query, new FieldMapping(self::fieldMapping))->execute($criteria);

//      TODO add nulls last to criteria library
        $orderByParts = $query->getQueryPart('orderBy');
        if (!empty($orderByParts)) {
            $query->resetQueryPart('orderBy');
            foreach ($orderByParts as $part) {
                $pieces = \explode(' ', $part, 2);
                $field = $pieces[0];
                $dir   = $pieces[1] ?? 'ASC';
                if (\str_starts_with($field, 'b.')) {
                    $dir .= ' NULLS LAST';
                }
                $query->addOrderBy($field, $dir);
            }
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function searchOne(Criteria $criteria): ?KeyforgeGame
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

    public function all(?int $offset = null, ?int $limit = null): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC');

        if (null !== $offset) {
            $query->setFirstResult($offset);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('count(a.*)')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_GAME_LOG, 'b', 'a.id = b.game_id');

        new DbalCriteriaAdapter($query, new FieldMapping(self::fieldMapping))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function save(KeyforgeGame $game): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date, created_at, winner_chains, loser_chains, competition, notes, approved, created_by)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date, :created_at, :winner_chains, :loser_chains, :competition, :notes, :approved, :created_by)
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
                        loser_chains = :loser_chains,
                        competition = :competition,
                        notes = :notes,
                        approved = :approved,
                        created_by = :created_by
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
        $stmt->bindValue(':competition', $game->competition()->name);
        $stmt->bindValue(':notes', $game->notes());
        $stmt->bindValue(':approved', $game->approved(), ParameterType::BOOLEAN);
        $stmt->bindValue(':created_by', $game->createdBy()?->value());

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

    public function saveLog(KeyforgeGameLog $gameLog): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, game_id, log, created_by, created_at,
                        turns, winner_amber_obtained, winner_amber_stolen, winner_cards_played, winner_cards_drawn, winner_cards_discarded, winner_keys_forged, winner_fights, winner_reaps, winner_extra_turns,
                        loser_amber_obtained, loser_amber_stolen, loser_cards_played, loser_cards_drawn, loser_cards_discarded, loser_keys_forged, loser_fights, loser_reaps, loser_extra_turns,
                        total_amber_obtained, total_amber_stolen, total_cards_played, total_cards_drawn, total_cards_discarded, total_keys_forged, total_fights, total_reaps, total_extra_turns)
                    VALUES (:id, :game_id, :log, :created_by, :created_at,
                        :turns, :winner_amber_obtained, :winner_amber_stolen, :winner_cards_played, :winner_cards_drawn, :winner_cards_discarded, :winner_keys_forged, :winner_fights, :winner_reaps, :winner_extra_turns,
                        :loser_amber_obtained, :loser_amber_stolen, :loser_cards_played, :loser_cards_drawn, :loser_cards_discarded, :loser_keys_forged, :loser_fights, :loser_reaps, :loser_extra_turns,
                        :total_amber_obtained, :total_amber_stolen, :total_cards_played, :total_cards_drawn, :total_cards_discarded, :total_keys_forged, :total_fights, :total_reaps, :total_extra_turns)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        game_id = :game_id,
                        log = :log,
                        created_by = :created_by,
                        created_at = :created_at,
                        turns = :turns,
                        winner_amber_obtained = :winner_amber_obtained,
                        winner_amber_stolen = :winner_amber_stolen,
                        winner_cards_played = :winner_cards_played,
                        winner_cards_drawn = :winner_cards_drawn,
                        winner_cards_discarded = :winner_cards_discarded,
                        winner_keys_forged = :winner_keys_forged,
                        winner_fights = :winner_fights,
                        winner_reaps = :winner_reaps,
                        winner_extra_turns = :winner_extra_turns,
                        loser_amber_obtained = :loser_amber_obtained,
                        loser_amber_stolen = :loser_amber_stolen,
                        loser_cards_played = :loser_cards_played,
                        loser_cards_drawn = :loser_cards_drawn,
                        loser_cards_discarded = :loser_cards_discarded,
                        loser_keys_forged = :loser_keys_forged,
                        loser_fights = :loser_fights,
                        loser_reaps = :loser_reaps,
                        loser_extra_turns = :loser_extra_turns,
                        total_amber_obtained = :total_amber_obtained,
                        total_amber_stolen = :total_amber_stolen,
                        total_cards_played = :total_cards_played,
                        total_cards_drawn = :total_cards_drawn,
                        total_cards_discarded = :total_cards_discarded,
                        total_keys_forged = :total_keys_forged,
                        total_fights = :total_fights,
                        total_reaps = :total_reaps,
                        total_extra_turns = :total_extra_turns
                    ',
                self::TABLE_GAME_LOG,
            ),
        );

        $stmt->bindValue(':id', $gameLog->id->value());
        $stmt->bindValue(':game_id', $gameLog->gameId?->value());
        $stmt->bindValue(':log', Json::encode($gameLog->log));
        $stmt->bindValue(':created_by', $gameLog->createdBy?->value());
        $stmt->bindValue(':created_at', $gameLog->createdAt->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':turns', $gameLog->turns);
        $stmt->bindValue(':winner_amber_obtained', $gameLog->winnerAmberObtained);
        $stmt->bindValue(':winner_amber_stolen', $gameLog->winnerAmberStolen);
        $stmt->bindValue(':winner_cards_played', $gameLog->winnerCardsPlayed);
        $stmt->bindValue(':winner_cards_drawn', $gameLog->winnerCardsDrawn);
        $stmt->bindValue(':winner_cards_discarded', $gameLog->winnerCardsDiscarded);
        $stmt->bindValue(':winner_keys_forged', $gameLog->winnerKeysForged);
        $stmt->bindValue(':winner_fights', $gameLog->winnerFights);
        $stmt->bindValue(':winner_reaps', $gameLog->winnerReaps);
        $stmt->bindValue(':winner_extra_turns', $gameLog->winnerExtraTurns);
        $stmt->bindValue(':loser_amber_obtained', $gameLog->loserAmberObtained);
        $stmt->bindValue(':loser_amber_stolen', $gameLog->loserAmberStolen);
        $stmt->bindValue(':loser_cards_played', $gameLog->loserCardsPlayed);
        $stmt->bindValue(':loser_cards_drawn', $gameLog->loserCardsDrawn);
        $stmt->bindValue(':loser_cards_discarded', $gameLog->loserCardsDiscarded);
        $stmt->bindValue(':loser_keys_forged', $gameLog->loserKeysForged);
        $stmt->bindValue(':loser_fights', $gameLog->loserFights);
        $stmt->bindValue(':loser_reaps', $gameLog->loserReaps);
        $stmt->bindValue(':loser_extra_turns', $gameLog->loserExtraTurns);
        $stmt->bindValue(':total_amber_obtained', $gameLog->totalAmberObtained);
        $stmt->bindValue(':total_amber_stolen', $gameLog->totalAmberStolen);
        $stmt->bindValue(':total_cards_played', $gameLog->totalCardsPlayed);
        $stmt->bindValue(':total_cards_drawn', $gameLog->totalCardsDrawn);
        $stmt->bindValue(':total_cards_discarded', $gameLog->totalCardsDiscarded);
        $stmt->bindValue(':total_keys_forged', $gameLog->totalKeysForged);
        $stmt->bindValue(':total_fights', $gameLog->totalFights);
        $stmt->bindValue(':total_reaps', $gameLog->totalReaps);
        $stmt->bindValue(':total_extra_turns', $gameLog->totalExtraTurns);

        $stmt->executeStatement();
    }

    public function gameLog(Uuid $id): ?KeyforgeGameLog
    {
        $log = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_GAME_LOG, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();;

        if (null === $log || false === $log) {
            return null;
        }

        return $this->mapGameLog($log);
    }

    public function allLogs(?int $offset = null, ?int $limit = null): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE_GAME_LOG, 'a')
            ->orderBy('a.created_at', 'ASC');

        if (null !== $offset) {
            $query->setFirstResult($offset);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $log) => $this->mapGameLog($log), $result);
    }

    private function map(array $game): KeyforgeGame
    {
        $score = Json::decode($game['score']);

        $statKeys = [
            'turns',
            'winner_amber_obtained', 'winner_amber_stolen', 'winner_cards_played', 'winner_cards_drawn', 'winner_cards_discarded', 'winner_keys_forged', 'winner_fights', 'winner_reaps', 'winner_extra_turns',
            'loser_amber_obtained', 'loser_amber_stolen', 'loser_cards_played', 'loser_cards_drawn', 'loser_cards_discarded', 'loser_keys_forged', 'loser_fights', 'loser_reaps', 'loser_extra_turns',
            'total_amber_obtained', 'total_amber_stolen', 'total_cards_played', 'total_cards_drawn', 'total_cards_discarded', 'total_keys_forged', 'total_fights', 'total_reaps', 'total_extra_turns',
        ];

        $logStats = null;
        if (isset($game['turns']) || isset($game['winner_amber_obtained'])) {
            $logStats = [];
            foreach ($statKeys as $k) {
                $logStats[$k] = isset($game[$k]) ? (int) $game[$k] : null;
            }
        }

        return new KeyforgeGame(
            Uuid::from($game['id']),
            Uuid::from($game['winner']),
            Uuid::from($game['loser']),
            Uuid::from($game['winner_deck']),
            Uuid::from($game['loser_deck']),
            $game['winner_chains'],
            $game['loser_chains'],
            Uuid::fromNullable($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
            new \DateTimeImmutable($game['created_at']),
            KeyforgeCompetition::fromName($game['competition']),
            $game['notes'],
            $game['approved'],
            Uuid::fromNullable($game['created_by']),
            Uuid::fromNullable($game['log_id']),
            $logStats,
        );
    }

    private function mapGameLog(array $log): KeyforgeGameLog
    {
        return new KeyforgeGameLog(
            Uuid::from($log['id']),
            Uuid::fromNullable($log['game_id']),
            Json::decode($log['log']),
            Uuid::fromNullable($log['created_by']),
            new \DateTimeImmutable($log['created_at']),
            isset($log['turns']) ? (int) $log['turns'] : null,
            isset($log['winner_amber_obtained']) ? (int) $log['winner_amber_obtained'] : null,
            isset($log['winner_amber_stolen']) ? (int) $log['winner_amber_stolen'] : null,
            isset($log['winner_cards_played']) ? (int) $log['winner_cards_played'] : null,
            isset($log['winner_cards_drawn']) ? (int) $log['winner_cards_drawn'] : null,
            isset($log['winner_cards_discarded']) ? (int) $log['winner_cards_discarded'] : null,
            isset($log['winner_keys_forged']) ? (int) $log['winner_keys_forged'] : null,
            isset($log['winner_fights']) ? (int) $log['winner_fights'] : null,
            isset($log['winner_reaps']) ? (int) $log['winner_reaps'] : null,
            isset($log['winner_extra_turns']) ? (int) $log['winner_extra_turns'] : null,
            isset($log['loser_amber_obtained']) ? (int) $log['loser_amber_obtained'] : null,
            isset($log['loser_amber_stolen']) ? (int) $log['loser_amber_stolen'] : null,
            isset($log['loser_cards_played']) ? (int) $log['loser_cards_played'] : null,
            isset($log['loser_cards_drawn']) ? (int) $log['loser_cards_drawn'] : null,
            isset($log['loser_cards_discarded']) ? (int) $log['loser_cards_discarded'] : null,
            isset($log['loser_keys_forged']) ? (int) $log['loser_keys_forged'] : null,
            isset($log['loser_fights']) ? (int) $log['loser_fights'] : null,
            isset($log['loser_reaps']) ? (int) $log['loser_reaps'] : null,
            isset($log['loser_extra_turns']) ? (int) $log['loser_extra_turns'] : null,
            isset($log['total_amber_obtained']) ? (int) $log['total_amber_obtained'] : null,
            isset($log['total_amber_stolen']) ? (int) $log['total_amber_stolen'] : null,
            isset($log['total_cards_played']) ? (int) $log['total_cards_played'] : null,
            isset($log['total_cards_drawn']) ? (int) $log['total_cards_drawn'] : null,
            isset($log['total_cards_discarded']) ? (int) $log['total_cards_discarded'] : null,
            isset($log['total_keys_forged']) ? (int) $log['total_keys_forged'] : null,
            isset($log['total_fights']) ? (int) $log['total_fights'] : null,
            isset($log['total_reaps']) ? (int) $log['total_reaps'] : null,
            isset($log['total_extra_turns']) ? (int) $log['total_extra_turns'] : null,
        );
    }
}
