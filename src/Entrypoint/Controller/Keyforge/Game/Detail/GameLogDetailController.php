<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Detail;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGameLogQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
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

        $eventTypes = [
            EventType::AMBER_OBTAINED->value => EventType::AMBER_OBTAINED,
            EventType::CARDS_DISCARDED->value => EventType::CARDS_DISCARDED,
            EventType::CARDS_DRAWN->value => EventType::CARDS_DRAWN,
            EventType::CARDS_PLAYED->value => EventType::CARDS_PLAYED,
            EventType::HOUSE_CHOSEN->value => EventType::HOUSE_CHOSEN,
            EventType::KEY_FORGED->value => EventType::KEY_FORGED,
            EventType::FIGHT->value => EventType::FIGHT,
            EventType::REAP->value => EventType::REAP,
            EventType::CARD_USED->value => EventType::CARD_USED,
            EventType::AMBER_STOLEN->value => EventType::AMBER_STOLEN,
            EventType::EXTRA_TURN->value => EventType::EXTRA_TURN,
            EventType::TOKEN_CREATED->value => EventType::TOKEN_CREATED,
            EventType::PROPHECY_ACTIVATED->value => EventType::PROPHECY_ACTIVATED,
            EventType::PROPHECY_FULFILLED->value => EventType::PROPHECY_FULFILLED,
            EventType::FATE_RESOLVED->value => EventType::FATE_RESOLVED,
            EventType::TIDE_RAISED->value => EventType::TIDE_RAISED,
            EventType::CHAINS_ADDED->value => EventType::CHAINS_ADDED,
            EventType::CHAINS_REDUCED->value => EventType::CHAINS_REDUCED,
            EventType::CHECK_DECLARED->value => EventType::CHECK_DECLARED,
            EventType::PLAYER_CONCEDED->value => EventType::PLAYER_CONCEDED,
        ];

        return $this->render(
            'Keyforge/Game/Detail/game.html.twig',
            [
                'gameId' => $log->gameId?->value(),
                'date' => $log->createdAt->format('Y-m-d'),
                'log' => new GameLogParser()->execute($log->log, ParseType::ARRAY),
                'eventTypes' => $eventTypes,
            ],
        );
    }
}
