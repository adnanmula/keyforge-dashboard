<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserDetailController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private UserRepository $userRepository,
        private KeyforgeUserRepository $kfUserRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(string $userId): Response
    {
        $this->assertIsLogged(UserRole::ROLE_KEYFORGE);

        $user = $this->userRepository->byId(Uuid::from($userId));

        $indexedFriends = [];
        $winrateIndexed = [
            KeyforgeCompetition::FRIENDS->name => ['total_games' => 0, 'total_wins' => 0, 'total_losses' => 0, 'win_ratio' => 0],
            KeyforgeCompetition::TCO_CASUAL->name => ['total_games' => 0, 'total_wins' => 0, 'total_losses' => 0, 'win_ratio' => 0],
            KeyforgeCompetition::TCO_COMPETITIVE->name => ['total_games' => 0, 'total_wins' => 0, 'total_losses' => 0, 'win_ratio' => 0],
        ];
        $deckStats = [];

        if (null !== $user) {
            $friends = $this->userRepository->friends($user->id(), false);

            foreach ($friends as $friend) {
                $indexedFriends[$friend['id']] = $friend['sender_name'];
                $indexedFriends[$friend['friend_id']] = $friend['receiver_name'];
            }

            $winrate = $this->kfUserRepository->winrate($user->id());

            $competitions = [KeyforgeCompetition::FRIENDS->name, KeyforgeCompetition::TCO_CASUAL->name, KeyforgeCompetition::TCO_COMPETITIVE->name];

            foreach ($winrate as $wr) {
                if (false === \in_array($wr['competition'], $competitions, true)) {
                    continue;
                }

                $winrateIndexed[$wr['competition']] = [
                    'total_games' => $wr['total_games'],
                    'total_wins' => $wr['total_wins'],
                    'total_losses' => $wr['total_games'] - $wr['total_wins'],
                    'win_ratio' => $wr['win_ratio'],
                ];
            }

            $deckStats = $this->kfUserRepository->bestDecks($user->id());
        }

        return $this->render(
            'Keyforge/User/user_detail.html.twig',
            [
                'reference' => $userId,
                'userId' => $userId,
                'username' => $user?->name(),
                'indexed_friends' => $indexedFriends,
                'wr_by_competition' => $winrateIndexed,
                'deck_stats' => $deckStats,
                'competition_wins' => $this->competitions($user),
            ],
        );
    }

    public function competitions(?User $user): array
    {
        if (null === $user) {
            return [];
        }

        return $this->competitionRepository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(new FilterField('winner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                ),
                null,
                null,
                new Sorting(
                    new Order(new FilterField('finished_at'), OrderType::ASC),
                ),
            ),
        );
    }
}
