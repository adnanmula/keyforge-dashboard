<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Detail;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\ParseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GameDetailController extends Controller
{
    public function __invoke(Request $request, string $id): Response
    {
        $games = $this->extractResult($this->bus->dispatch(new GetGamesQuery(ids: [$id])));

        $game = $games['games'][0] ?? null;

        if (null === $game) {
            throw new \RuntimeException('Game not found');
        }

        if (null === $game['log']) {
            throw new \RuntimeException('No information available');
        }

        $p = new GameLogParser();
        $parsedLog = $p->execute($game['log'], ParseType::ARRAY);

        $game['log'] = $parsedLog;

        return $this->render(
            'Keyforge/Game/Detail/game.html.twig',
            [
                'game' => $game,
            ],
        );
    }
}
