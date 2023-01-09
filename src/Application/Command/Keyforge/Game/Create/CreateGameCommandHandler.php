<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;

final class CreateGameCommandHandler
{
    public function __construct(
        private KeyforgeUserRepository $userRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
        private ImportDeckService $importDeckService,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        [$winner, $loser, $firstTurn] = $this->getUsers($command->winner(), $command->loser(), $command->firstTurn());
        [$winnerDeck, $loserDeck] = $this->getDecks($command->winnerDeck(), $command->loserDeck());

        $game = new KeyforgeGame(
            Uuid::v4(),
            Uuid::from($winner),
            Uuid::from($loser),
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->winnerChains(),
            $command->loserChains(),
            Uuid::from($firstTurn),
            KeyforgeGameScore::from(3, $command->loserScore()),
            $command->date(),
            new \DateTimeImmutable(),
            $command->competition(),
            $command->notes(),
        );

        $this->gameRepository->save($game);

        $this->updateDeckWinRate($winnerDeck, $loserDeck);

        if (null !== $command->fixtureId()) {
            $fixture = $this->competitionRepository->fixtureById(Uuid::from($command->fixtureId()));
            $fixture->updateGames($game->id());
            $fixture->updateWinner(Uuid::from($winner));
            $fixture->updatePlayedAt($command->date());
            $this->competitionRepository->saveFixture($fixture);
        }
    }

    private function updateDeckWinRate(KeyforgeDeck $winnerDeck, KeyforgeDeck $loserDeck): void
    {
        $games1 = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        $games2 = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($loserDeck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($loserDeck->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        $deck1Wins = 0;
        $deck1Losses = 0;

        foreach ($games1 as $game) {
            if ($game->winnerDeck()->equalTo($winnerDeck->id())) {
                $deck1Wins++;
            }

            if ($game->loserDeck()->equalTo($winnerDeck->id())) {
                $deck1Losses++;
            }
        }

        $deck2Wins = 0;
        $deck2Losses = 0;

        foreach ($games2 as $game) {
            if ($game->winnerDeck()->equalTo($loserDeck->id())) {
                $deck2Wins++;
            }

            if ($game->loserDeck()->equalTo($loserDeck->id())) {
                $deck2Losses++;
            }
        }

        $winnerDeck->updateWins($deck1Wins)->updateLosses($deck1Losses);
        $loserDeck->updateWins($deck2Wins)->updateLosses($deck2Losses);

        $this->deckRepository->save($winnerDeck);
        $this->deckRepository->save($loserDeck);
    }

    private function getUsers(string $winner, string $loser, string $firstTurn): array
    {
        if (false === Uuid::isValid($winner)) {
            $winner = $this->fetchUserOrCreate($winner)->id()->value();
        }

        if (false === Uuid::isValid($loser)) {
            $loser = $this->fetchUserOrCreate($loser)->id()->value();
        }

        if (false === Uuid::isValid($firstTurn)) {
            $firstTurn = $this->fetchUserOrCreate($firstTurn)->id()->value();
        }

        return [$winner, $loser, $firstTurn];
    }

    private function fetchUserOrCreate(string $name): KeyforgeUser
    {
        $user = $this->userRepository->byName($name);

        if (null === $user) {
            $user = KeyforgeUser::create(Uuid::v4(), $name, true);
            $this->userRepository->save($user);
        }

        return $user;
    }

    private function getDecks(string $winnerDeck, string $loserDeck): array
    {
        $winnerDeck = Uuid::isValid($winnerDeck)
            ? $this->importDeckService->execute(Uuid::from($winnerDeck), null)
            : $this->deckRepository->byNames($winnerDeck)[0] ?? null;

        $loserDeck = Uuid::isValid($loserDeck)
            ? $this->importDeckService->execute(Uuid::from($loserDeck), null)
            : $this->deckRepository->byNames($loserDeck)[0] ?? null;

        if (null === $winnerDeck || null === $loserDeck) {
            throw new \Exception('Deck not found');
        }

        return [$winnerDeck, $loserDeck];
    }
}
