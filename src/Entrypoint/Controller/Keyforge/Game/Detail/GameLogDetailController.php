<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Detail;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGameLogQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\ParseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GameLogDetailController extends Controller
{
    public function __invoke(Request $request, string $id): Response
    {
        /** @var ?KeyforgeGameLog $log */
        $log = $this->extractResult($this->bus->dispatch(new GetGameLogQuery($id)));

        if (null === $log) {
            throw new \RuntimeException('Gamelog not found');
        }

        return $this->render(
            'Keyforge/Game/Detail/game.html.twig',
            [
                'game' => [
                    'date' => $log->createdAt->format('Y-m-d'),
                    'log' => new GameLogParser()->execute($log->log, ParseType::ARRAY),
                ],
            ],
        );
    }
}
