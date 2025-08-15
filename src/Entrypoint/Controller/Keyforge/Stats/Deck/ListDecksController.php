<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Tag\GetTagsQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ListDecksController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private UserRepository $userRepository,
        private KeyforgeUserRepository $kfUserRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(): Response
    {
        $tags = $this->extractResult($this->bus->dispatch(new GetTagsQuery(
            TagVisibility::PUBLIC->name,
            null,
            null,
        )));

        return $this->render(
            'Keyforge/Stats/Deck/list_decks.html.twig',
            [
                'owner' => null,
                'tags' => \array_map(static fn (KeyforgeDeckTag $tag) => $tag->jsonSerialize(), $tags['tags']),
                'privateTags' => [],
                'users' => $this->users(),
                'sets' => KeyforgeSet::cases(),
                'houses' => KeyforgeHouse::cases(),
            ],
        );
    }

    private function users(): array
    {
        $user = $this->getUser();
        $users = [];

        if (null !== $user) {
            $friends = $this->userRepository->friends($user->id());
            $friendsIds = \array_map(static fn (array $f) => $f['id'], $friends);

            $users = $this->kfUserRepository->search(
                new Criteria(
                    new Filters(
                        FilterType::AND,
                        new Filter(
                            new FilterField('id'),
                            new StringArrayFilterValue($user->id()->value(), ...$friendsIds),
                            FilterOperator::IN,
                        ),
                    ),
                ),
            );
        }

        return $users;
    }
}
