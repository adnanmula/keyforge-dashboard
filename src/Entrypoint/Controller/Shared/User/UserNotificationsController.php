<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\User;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserNotificationsController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        private readonly UserRepository $userRepository,
        private readonly KeyforgeGameRepository $gameRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function count(Request $request): Response
    {
        try {
            $this->assertIsLogged(UserRole::ROLE_KEYFORGE);
        } catch (AccessDeniedException) {
            return new JsonResponse(['total' => 0, 'friend_requests' => 0, 'games_pending' => 0,], Response::HTTP_OK);
        }

        $user = $this->getUser();

        $friendRequests = \count(\array_filter(
            $this->userRepository->friends($user->id(), true),
            static fn (array $f): bool => $f['id'] !== $user->id()->value(),
        ));

        $gamesPending = $this->gameRepository->count(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('approved'), new IntFilterValue(0), FilterOperator::EQUAL),
                    new Filter(new FilterField('created_by'), new StringFilterValue($user->id()->value()), FilterOperator::NOT_EQUAL),
                ),
                new AndFilterGroup(
                    FilterType::OR,
                    new Filter(new FilterField('winner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('loser'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        return new JsonResponse([
            'total' => $friendRequests + $gamesPending,
            'friend_requests' => $friendRequests,
            'games_pending' => $gamesPending,
        ], Response::HTTP_OK);
    }
}
