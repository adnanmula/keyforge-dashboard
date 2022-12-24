<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Tag\GetTagsQuery;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListDecksController extends Controller
{
    public function __invoke(): Response
    {
        $tags = $this->extractResult($this->bus->dispatch(new GetTagsQuery(TagVisibility::PUBLIC->name)));

        return $this->render(
            'Keyforge/Stats/Deck/list_decks.html.twig',
            [
                'decks' => [],
                'tags' => $tags['tags'],
            ],
        );
    }
}
