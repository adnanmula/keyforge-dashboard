<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class FinishCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(FinishCompetitionCommand $command): void
    {
        $competition = $this->repository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringFilterValue($command->competitionId->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        $this->assert($competition);

        $competition->updateFinishedAt($command->date);
        $competition->updateWinner($command->winnerId);

        $this->repository->save($competition);
    }

    private function assert(?KeyforgeCompetition $competition): void
    {
        if (null === $competition) {
            throw new \Exception($this->translator->trans('competition.error.not_found'));
        }

        if (null === $competition->startedAt) {
            throw new \Exception($this->translator->trans('competition.error.not_started'));
        }

        if (null !== $competition->finishedAt) {
            throw new \Exception($this->translator->trans('competition.error.already_finished'));
        }
    }
}
