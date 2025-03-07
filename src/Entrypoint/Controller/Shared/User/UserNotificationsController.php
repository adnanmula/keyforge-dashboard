<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\User;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
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
        private readonly KeyforgeUserRepository $keyforgeUserRepository,
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly KeyforgeGameRepository $gameRepository,
        private readonly UpdateDeckWinRateService $updateDeckWinRateService,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function count(Request $request): Response
    {
        try {
            $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);
        } catch (AccessDeniedException) {
            return new JsonResponse(['total' => 0, 'friend_requests' => 0, 'games_pending' => 0,], Response::HTTP_OK);
        }

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
                ),
                new AndFilterGroup(
                    FilterType::OR,
                    new Filter(new FilterField('winner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('loser'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        $response = [
            'total' => $friendRequests + $gamesPending,
            'friend_requests' => $friendRequests,
            'games_pending' => $gamesPending,
        ];

        if ($user->getRoles()[0] === UserRole::ROLE_ADMIN->value) {
            $pendingUsers = \count($this->userRepository->byRoles(UserRole::ROLE_BASIC));

            $response['new_users_pending'] = $pendingUsers;
            $response['total'] += $pendingUsers;
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function games(Request $request): Response
    {
        $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $error = null;

        try {
            $filters = [];

            $filters[] = new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('approved'), new IntFilterValue(0), FilterOperator::EQUAL),
            );

            if (false === $this->isGranted(UserRole::ROLE_ADMIN->value)) {
                $filters[] = new AndFilterGroup(
                    FilterType::OR,
                    new Filter(new FilterField('winner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('loser'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                );
            }

            $gamesPending = $this->gameRepository->search(new Criteria(null, null, null, ...$filters));

            $deckIds = \array_values(\array_unique(\array_merge(
                \array_map(static fn (KeyforgeGame $g) => $g->winnerDeck()->value(), $gamesPending),
                \array_map(static fn (KeyforgeGame $g) => $g->loserDeck()->value(), $gamesPending),
            )));

            $decks = $this->deckRepository->search(
                new Criteria(
                    null,
                    null,
                    null,
                    new AndFilterGroup(
                        FilterType::AND,
                        new Filter(new FilterField('id'), new StringArrayFilterValue(...$deckIds), FilterOperator::IN),
                    ),
                ),
            );

            $indexedDecks = [];

            foreach ($decks as $deck) {
                $indexedDecks[$deck->id()->value()] = $deck->name();
            }

            $userIds = \array_values(\array_unique(\array_filter(\array_merge(
                \array_map(static fn (KeyforgeGame $g) => $g->winner()->value(), $gamesPending),
                \array_map(static fn (KeyforgeGame $g) => $g->loser()->value(), $gamesPending),
                \array_map(static fn (KeyforgeGame $g) => $g->createdBy()?->value(), $gamesPending),
            ))));

            $users = $this->keyforgeUserRepository->search(
                new Criteria(
                    null,
                    null,
                    null,
                    new AndFilterGroup(
                        FilterType::AND,
                        new Filter(new FilterField('id'), new StringArrayFilterValue(...$userIds), FilterOperator::IN),
                    ),
                ),
            );

            $indexedUser = [];

            foreach ($users as $user) {
                $indexedUser[$user->id()->value()] = $user->name();
            }

            $response = [];

            foreach ($gamesPending as $game) {
                if (null === $game->createdBy()) {
                    continue;
                }

                $approvalPendingBy = $game->winner()->value() === $game->createdBy()->value()
                    ? $game->loser()->value()
                    : $game->winner()->value();

                $response[] = [
                    'id' => $game->id()->value(),
                    'winner_id' => $game->winner()->value(),
                    'loser_id' => $game->loser()->value(),
                    'winner_name' => $indexedUser[$game->winner()->value()] ?? '',
                    'loser_name' => $indexedUser[$game->loser()->value()] ?? '',
                    'winner_deck_id' => $game->winnerDeck()->value(),
                    'loser_deck_id' => $game->loserDeck()->value(),
                    'winner_deck_name' => $indexedDecks[$game->winnerDeck()->value()] ?? '',
                    'loser_deck_name' => $indexedDecks[$game->loserDeck()->value()] ?? '',
                    'score' => $game->score()->winnerScore() . '/' . $game->score()->loserScore(),
                    'created_by' => $game->createdBy()->value(),
                    'created_by_name' => $indexedUser[$game->createdBy()->value()] ?? '',
                    'approval_pending_by' => $approvalPendingBy,
                    'approval_pending_by_name' => $indexedUser[$approvalPendingBy] ?? '',
                ];
            }
        } catch (\Throwable $e) {
            $response = [];
            $error = $e->getMessage();
        }

        return $this->render('Shared/User/user_pending_games.html.twig', [
            'error' => $error,
            'pendingGames' => $response,
        ]);
    }

    public function acceptGame(Request $request): Response
    {
        $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $gameId = $request->get('game');

        if (false === Uuid::isValid($gameId)) {
            throw new \Exception('Invalid');
        }

        $game = $this->gameRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($gameId), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $game) {
            throw new \Exception('Game not found');
        }

        if ($game->approved()) {
            return new Response('', Response::HTTP_OK);
        }

        if (false === $this->isGranted(UserRole::ROLE_ADMIN->value)) {
            if (null === $game->createdBy()) {
                throw new \Exception('Error');
            }

            if ($game->createdBy()->equalTo($user->id())) {
                throw new \Exception('Error');
            }

            if (false === $game->winner()->equalTo($user->id()) && false === $game->loser()->equalTo($user->id())) {
                throw new \Exception('Error');
            }
        }

        $game->approve();

        $this->gameRepository->save($game);

        $this->updateDeckWinRateService->execute($game->winnerDeck());
        $this->updateDeckWinRateService->execute($game->loserDeck());

        return new Response('', Response::HTTP_OK);
    }

    public function rejectGame(Request $request): Response
    {
        $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $gameId = $request->get('game');

        if (false === Uuid::isValid($gameId)) {
            throw new \Exception('Invalid');
        }

        $game = $this->gameRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($gameId), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $game) {
            throw new \Exception('Game not found');
        }

        if ($game->approved()) {
            throw new \Exception('Game is approved');
        }

        if (false === $this->isGranted(UserRole::ROLE_ADMIN->value)) {
            if (null === $game->createdBy()) {
                throw new \Exception('Error');
            }

            if ($game->createdBy()->equalTo($user->id())) {
                throw new \Exception('Error');
            }

            if (false === $game->winner()->equalTo($user->id()) && false === $game->loser()->equalTo($user->id())) {
                throw new \Exception('Error');
            }
        }

        $this->gameRepository->remove($game->id());

        return new Response('', Response::HTTP_OK);
    }
}
