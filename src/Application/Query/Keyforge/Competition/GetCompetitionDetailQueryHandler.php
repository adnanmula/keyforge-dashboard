<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Tournament\Classification\Player;
use AdnanMula\Tournament\Classification\User as TournamentUser;

final readonly class GetCompetitionDetailQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeUserRepository $userRepository,
    ) {}

    public function __invoke(GetCompetitionDetailQuery $query): array
    {
        $competition = $this->competition($query->id);
        $users = $this->users($competition);
        $fixtures = $this->repository->fixtures($competition->id);

        foreach ($competition->players as $index => $player) {
            $competition->classification->addPlayer(new Player(
                $index + 1,
                new TournamentUser($player, $users[$player]->name()),
                0,
                0,
                0,
                0,
                0,
                0,
            ));
        }

        if (null !== $competition->startedAt) {
            $gameIds = [];

            foreach ($fixtures as $fixture) {
                $competition->fixtures->add($fixture);

                $gameIds[] = array_merge($gameIds, $fixture->games);
            }

            $games = $this->games(...$gameIds);


            foreach ($games as $game) {

            }
        }

        return [
            'competition' => $competition,
        ];
    }

    private function competition(Uuid $id): KeyforgeCompetition
    {
        $competition = $this->repository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringFilterValue($id->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        return $competition;
    }

    /** @return array<string, User> */
    private function users(KeyforgeCompetition $competition): array
    {
        $users = $this->userRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(
                    new FilterField('id'),
                    new StringArrayFilterValue(...$competition->players),
                    FilterOperator::IN,
                ),
            ),
        ));

        $indexedUsers = [];

        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user;
        }

        return $indexedUsers;
    }

    /** @return array<string, KeyforgeGame> */
    private function games(string ...$ids): array
    {
        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$ids), FilterOperator::IN),
            ),
        ));

        $indexedGames = [];

        foreach ($games as $game) {
            $indexedGames[$game->id()->value()] = $game;
        }

        return $indexedGames;
    }
}
