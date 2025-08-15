<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\FriendAccept;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class AcceptFriendCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private KeyforgeDeckRepository $deckRepository,
        private UpdateDeckWinRateService $updateDeckWinRateService,
    ) {}

    public function __invoke(AcceptFriendCommand $command): void
    {
        $user = $this->repository->byId($command->user);
        $friend = $this->repository->byId($command->friendId);

        if (null === $user || null === $friend) {
            throw new UserNotExistsException();
        }

        $request = $this->repository->friendRequest($friend->id(), $command->user);

        if (null === $request) {
            return;
        }

        $this->repository->addFriend($user->id(), $friend->id(), false);
        $this->repository->addFriend($friend->id(), $user->id(), false);

        $decks = $this->deckRepository->search(
            new Criteria(
                new Filters(
                    FilterType::OR,
                    new Filter(new FilterField('owner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('owner'), new StringFilterValue($friend->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        foreach ($decks as $deck) {
            $this->updateDeckWinRateService->execute($deck->id());
        }
    }
}
