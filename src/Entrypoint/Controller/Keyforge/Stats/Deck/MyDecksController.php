<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Tag\GetTagsQuery;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

final class MyDecksController extends Controller
{
    private Security $security;

    public function __construct(MessageBusInterface $bus, Security $security)
    {
        $this->security = $security;

        parent::__construct($bus);
    }

    public function __invoke(): Response
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            return new Response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $tags = $this->extractResult($this->bus->dispatch(new GetTagsQuery(
            null,
            null,
        )));

        return $this->render(
            'Keyforge/Stats/Deck/list_my_decks.html.twig',
            [
                'owner' => $user->id()->value(),
                'tags' => $tags['tags'],
            ],
        );
    }
}
