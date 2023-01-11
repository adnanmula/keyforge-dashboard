<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Tag\GetTagsQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            throw new AccessDeniedException();
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
                'tags' => \array_map(static fn (KeyforgeTag $tag) => $tag->jsonSerialize(), $tags['tags']),
            ],
        );
    }
}
