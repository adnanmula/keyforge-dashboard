<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Detail;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GameAnalyzeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $log = $request->get('log');

        if (null === $log) {
            return $this->render(
                'Keyforge/Game/Detail/game_analyze.html.twig',
                [
                    'error' => null,
                ],
            );
        }

        try {
            $p = new GameLogParser();
            $parsedLog = $p->execute($log);
        } catch (\Throwable $e) {
            return $this->render(
                'Keyforge/Game/Detail/game_analyze.html.twig',
                [
                    'error' => $e->getMessage(),
                ],
            );
        }

        return $this->render(
            'Keyforge/Game/Detail/game.html.twig',
            [
                'game' => [
                    'date' => new \DateTimeImmutable()->format('Y-m-d'),
                    'log' => $parsedLog,
                ],
            ],
        );
    }
}
