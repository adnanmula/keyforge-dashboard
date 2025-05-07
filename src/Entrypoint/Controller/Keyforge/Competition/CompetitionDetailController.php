<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Query\Keyforge\Competition\GetCompetitionDetailQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Sorting\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompetitionDetailController extends Controller
{
    public function __invoke(Request $request, string $id): Response
    {
        $this->assertIsLogged();

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(null, null, false, false)),
        );

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->jsonSerialize();
        }

        $competition = $this->extractResult(
            $this->bus->dispatch(new GetCompetitionDetailQuery($id)),
        );

        $indexedGames = $this->games($competition);
        $indexedDecks = $this->decks($competition, $indexedGames);

        return $this->render('Keyforge/Competition/competition_detail.html.twig', [
            'users' => \array_map(static fn (KeyforgeUser $user) => $user->jsonSerialize(), $users),
            'competition' => $competition,
            'indexedUsers' => $indexedUsers,
            'indexedDecks' => $indexedDecks,
            'indexedGames' => $indexedGames,
        ]);
    }

    private function decks(KeyforgeCompetition $competition, array $games): array
    {
        $fixtures = $competition->fixtures->groupedByReference();

        if (0 === count($fixtures)) {
            return [];
        }

        $ids = [];

        foreach ($fixtures as $round) {
            /** @var KeyforgeCompetitionFixture $fixture */
            foreach ($round as $fixture) {
                foreach ($fixture->games as $gameId) {
                    $game = $games[$gameId->value()] ?? null;
                    if (null === $game) {
                        continue;
                    }

                    $ids[] = $game['winner_deck'];
                    $ids[] = $game['loser_deck'];
                }
            }
        }

        $ids = array_unique($ids);

        $decks = $this->extractResult(
            $this->bus->dispatch(
                new GetDecksQuery(
                    orderField: 'name',
                    orderDirection: OrderType::ASC->value,
                    deckTypes: [KeyforgeDeckType::STANDARD->value],
                    deckIds: $ids,
                ),
            ),
        );

        $decks = \array_map(static fn (KeyforgeDeck $deck) => ['id' => $deck->id()->value(), 'name' => $deck->name()], $decks['decks']);

        $indexedDecks = [];

        foreach ($decks as $deck) {
            $indexedDecks[$deck['id']] = $deck['name'];
        }

        return $indexedDecks;
    }

    /** @return array<string, array> */
    private function games(KeyforgeCompetition $competition): array
    {
        $games = [];

        if (null !== $competition->startedAt) {
            $gameIds = [];

            /** @var KeyforgeCompetitionFixture $fixture */
            foreach ($competition->fixtures->fixtures as $fixture) {
                $gameIds = array_merge($gameIds, $fixture->games);
            }

            if (count($gameIds) > 0) {
                $games = $this->searchGames(...$gameIds);
            }
        }

        return $games;
    }

    /** @return array<string, array> */
    private function searchGames(Uuid ...$ids): array
    {
        $query = new GetGamesQuery(ids: \array_map(static fn ($id) => $id->value(), $ids));
        $result = $this->extractResult($this->bus->dispatch($query));

        $indexedGames = [];

        foreach ($result['games'] as $game) {
            $indexedGames[$game['id']] = $game;
        }

        return $indexedGames;
    }
}
