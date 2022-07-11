<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\Get\GetKeyforgeDecksQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class ListDecksController extends AbstractController
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(Request $request): Response
    {
        $decks = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeDecksQuery(0, 25)),
        );

        return $this->render('Keyforge/list_decks.html.twig', ['decks' => $decks]);
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
