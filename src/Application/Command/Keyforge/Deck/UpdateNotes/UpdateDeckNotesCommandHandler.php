<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final class UpdateDeckNotesCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeDeckUserDataRepository $userDataRepository,
    ) {}

    public function __invoke(UpdateDeckNotesCommand $command): void
    {
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

        $userData = $this->userDataRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($command->deckId->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('owner'), new StringFilterValue($command->userId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $userData) {
            throw new \Exception('You are not the owner of this deck.');
        }

        $userData->setNotes($command->notes);

        $this->userDataRepository->save($userData);
    }
}
