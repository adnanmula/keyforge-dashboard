<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CreateCompetitionGameCommandHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
        private UpdateDeckWinRateService $updateDeckWinRateService,
        private Security $security,
    ) {}

    public function __invoke(CreateCompetitionGameCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception();
        }

        [$winnerDeck, $loserDeck] = $this->getDecks($command->winnerDeck, $command->loserDeck);

        $game = new KeyforgeGame(
            Uuid::v4(),
            $command->winner,
            $command->loser,
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->winnerChains,
            $command->loserChains,
            $command->firstTurn,
            KeyforgeGameScore::from(3, $command->loserScore),
            $command->date,
            new \DateTimeImmutable(),
            $command->competition,
            $command->notes,
            false,
            $user->id(),
            null,
        );

        $this->gameRepository->save($game);

        $this->updateDeckWinRateService->execute($winnerDeck->id());
        $this->updateDeckWinRateService->execute($loserDeck->id());

        $this->updateFixture($command, $game);
    }

    private function updateFixture(CreateCompetitionGameCommand $command, KeyforgeGame $game): void
    {
        $fixture = $this->competitionRepository->fixtureById($command->fixtureId);

        if (null === $fixture) {
            return;
        }

        $fixture->addGame($game->id());
        $fixture->updateWinner($this->fixtureWinner($fixture, $game));
        $fixture->updatePlayedAt($command->date);

        $this->competitionRepository->saveFixture($fixture);
    }

    /** @return array{KeyforgeDeck, KeyforgeDeck} */
    private function getDecks(string $winnerDeck, string $loserDeck): array
    {
        $winnerDeck = $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($winnerDeck), FilterOperator::EQUAL),
                ),
            ),
        );

        $loserDeck = $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($loserDeck), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $winnerDeck || null === $loserDeck) {
            throw new \Exception('Deck not found');
        }

        return [$winnerDeck, $loserDeck];
    }

    private function fixtureWinner(KeyforgeCompetitionFixture $fixture, KeyforgeGame $game): ?Uuid
    {
        if ($fixture->type() === CompetitionFixtureType::BEST_OF_1) {
            return $game->winner();
        }

        $filters = [];

        foreach ($fixture->games() as $gameId) {
            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($gameId->value()), FilterOperator::EQUAL);
        }

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(FilterType::OR, ...$filters),
        ));

        $winners = [
            $fixture->users()[0]->value() => 0,
            $fixture->users()[1]->value() => 0,
        ];

        foreach ($games as $game) {
            $winners[$game->winner()->value()] += 1;
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_3) {
            return $this->checkWins($fixture, $winners, 2);
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_5) {
            return $this->checkWins($fixture, $winners, 3);
        }

        $gamesCount = $winners[$fixture->users()[0]->value()] + $winners[$fixture->users()[1]->value()];

        if ($fixture->type() === CompetitionFixtureType::GAMES_3) {
            if ($gamesCount === 3) {
                return $this->checkWins($fixture, $winners, 2);
            }

            return null;
        }

        if ($fixture->type() === CompetitionFixtureType::GAMES_5) {
            if ($gamesCount === 5) {
                return $this->checkWins($fixture, $winners, 3);
            }

            return null;
        }

        return null;
    }

    private function checkWins(KeyforgeCompetitionFixture $fixture, array $winners, int $expectedWins): ?Uuid
    {
        if ($winners[$fixture->users()[0]->value()] >= $expectedWins) {
            return $fixture->users()[0];
        }

        if ($winners[$fixture->users()[1]->value()] >= $expectedWins) {
            return $fixture->users()[1];
        }

        return null;
    }
}
