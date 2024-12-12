<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UpdateDeckNotesCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private Security $security,
    ) {}

    public function __invoke(UpdateDeckNotesCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Forbidden');
        }

        $deck = $this->repository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($command->deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $deck) {
            throw new \Exception('Deck not found.');
        }

        $decksOwnership = $this->repository->ownedBy($user->id());

        $isOwner = \count(\array_filter($decksOwnership, static fn (array $deckOwnership): bool => $deckOwnership['user_id'] === $user->id()->value())) > 0;

        if (false === $isOwner) {
            throw new \Exception('You are not the owner of this deck.');
        }

        $this->repository->updateNotes($user->id(), $deck->id(), $command->notes);
    }
}
