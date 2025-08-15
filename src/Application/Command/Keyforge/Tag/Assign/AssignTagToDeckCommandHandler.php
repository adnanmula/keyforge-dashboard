<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Assign;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class AssignTagToDeckCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private Security $security,
    ) {}

    public function __invoke(AssignTagToDeckCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Forbidden');
        }

        $deck = $this->repository->searchOne(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($command->deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $deck) {
            return;
        }

        $ownedDecks = $this->repository->ownedBy($user->id());
        $isOwner = null;

        foreach ($ownedDecks as $ownedDeck) {
            if ($deck->id()->value() === $ownedDeck['deck_id']) {
                $isOwner = $ownedDeck;
            }
        }

        if (null === $isOwner) {
            return;
        }

        $this->repository->updateUserTags($user->id(), $deck->id(), ...$command->tagIds);
    }
}
