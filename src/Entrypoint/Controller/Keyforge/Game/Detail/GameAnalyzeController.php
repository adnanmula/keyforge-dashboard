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

            if (null === $parsedLog->winner()) {
                throw new \Exception('Incomplete or malformed log');
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $this->render(
                'Keyforge/Game/Detail/game_analyze.html.twig',
                [
                    'error' => $this->translator->trans('menu.log_error'),
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
