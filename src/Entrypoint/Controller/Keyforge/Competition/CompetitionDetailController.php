<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Query\Keyforge\Competition\GetCompetitionDetailQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Sorting\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompetitionDetailController extends Controller
{
    public function __invoke(Request $request, string $reference): Response
    {
        $this->assertIsLogged();

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(null, null, false, false)),
        );

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(null, null, null, 'name', OrderType::ASC->value, null, null, null, [KeyforgeDeckType::STANDARD->value])),
        );

        $decks = \array_map(static fn (KeyforgeDeck $deck) => ['id' => $deck->id()->value(), 'name' => $deck->name()], $decks['decks']);

        $indexedDecks = [];
        foreach ($decks as $deck) {
            $indexedDecks[$deck['id']] = $deck['name'];
        }

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->jsonSerialize();
        }

        $detail = $this->extractResult(
            $this->bus->dispatch(new GetCompetitionDetailQuery($reference)),
        );

        return $this->render('Keyforge/Competition/competition_detail.html.twig', [
            'users' => \array_map(static fn (KeyforgeUser $user) => $user->jsonSerialize(), $users),
            'competition' => $detail['competition'],
            'fixtures' => $detail['fixtures'],
            'classification' => $detail['classification'],
            'winnerDecks' => $decks,
            'loserDecks' => $decks,
            'indexedDecks' => $indexedDecks,
            'indexedUsers' => $indexedUsers,
        ]);
    }
}
