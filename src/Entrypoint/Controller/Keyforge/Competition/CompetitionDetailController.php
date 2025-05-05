<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Query\Keyforge\Competition\GetCompetitionDetailQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
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

        $detail = $this->extractResult(
            $this->bus->dispatch(new GetCompetitionDetailQuery($id)),
        );

        $indexedDecks = $this->decks($detail['fixtures'] ?? null);

        /** @var KeyforgeCompetition $competition */
        $competition = $detail['competition'];

        return $this->render('Keyforge/Competition/competition_detail.html.twig', [
            'users' => \array_map(static fn (KeyforgeUser $user) => $user->jsonSerialize(), $users),
            'competition' => $competition,
            'fixtures' => $detail['fixtures'] ?? null,
            'indexedUsers' => $indexedUsers,
            'indexedDecks' => $indexedDecks,
        ]);
    }

    private function decks(?array $fixtures): array
    {
        if (null === $fixtures) {
            return [];
        }

        $ids = [];

        foreach ($fixtures as $round) {
            foreach ($round as $fixture) {
                foreach ($fixture['games'] as $game) {
                    $ids[] = $game['winnerDeck'];
                    $ids[] = $game['loserDeck'];
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
}
