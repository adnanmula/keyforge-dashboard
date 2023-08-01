<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Tag\GetTagsQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ListDecksController extends Controller
{
    private KeyforgeUserRepository $userRepository;

    public function __construct(MessageBusInterface $bus, Security $security, KeyforgeUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct($bus, $security);
    }

    public function __invoke(): Response
    {
        $tags = $this->extractResult($this->bus->dispatch(new GetTagsQuery(
            null,
            TagVisibility::PUBLIC->name,
            null,
        )));

        $users = $this->userRepository->all(false);

        return $this->render(
            'Keyforge/Stats/Deck/list_decks.html.twig',
            [
                'decks' => [],
                'tags' => \array_map(static fn (KeyforgeTag $tag) => $tag->jsonSerialize(), $tags['tags']),
                'users' => $users,
            ],
        );
    }
}
