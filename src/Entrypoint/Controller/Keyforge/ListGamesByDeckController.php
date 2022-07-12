<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\GetGames\GetKeyforgeGamesByDeckQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class ListGamesByDeckController extends AbstractController
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(Request $request, string $deckId): Response
    {
        $games = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeGamesByDeckQuery($deckId)),
        );

        return $this->render('Keyforge/list_games.html.twig', ['games' => $games]);
    }

    private function extractResult(mixed $message): mixed
    {
        $stamp = $message->last(HandledStamp::class);

        if (null === $stamp) {
            return null;
        }

        return $stamp->getResult();
    }
}
