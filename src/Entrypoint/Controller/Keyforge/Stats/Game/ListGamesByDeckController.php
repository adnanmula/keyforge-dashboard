<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Domain\Model\Shared\Filter;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerm;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\SearchTermType;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByDeckController extends Controller
{
    public function __invoke(string $deckId): Response
    {
        $games = $this->extractResult(
            $this->bus->dispatch(new GetGamesQuery(
                null,
                new SearchTerms(
                    new SearchTerm(
                        SearchTermType::OR,
                        new Filter('winner_deck', $deckId),
                        new Filter('loser_deck', $deckId),
                    ),
                ),
                null,
            )),
        );

        return $this->render(
            'Keyforge/Stats/Game/list_games_by_deck.html.twig',
            ['games' => $games, 'reference' => $deckId, 'name' => $this->getReferenceName($games['games'][0] ?? null, $deckId)],
        );
    }

    private function getReferenceName(?array $game, string $deckId): string
    {
        if (null === $game) {
            return '';
        }

        if ($game['winner_deck'] === $deckId) {
            return $game['winner_deck_name'];
        }

        if ($game['loser_deck'] === $deckId) {
            return $game['loser_deck_name'];
        }

        return '';
    }
}
