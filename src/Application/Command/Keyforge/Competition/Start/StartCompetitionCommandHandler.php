<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Start;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Tournament\Fixture\FixturesGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class StartCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(StartCompetitionCommand $command): void
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

        if (null === $competition) {
            throw new \Exception($this->translator->trans('competition.error.not_found'));
        }

        if (null !== $competition->startedAt) {
            throw new \Exception($this->translator->trans('competition.error.already_started'));
        }

        if (null !== $competition->finishedAt) {
            throw new \Exception($this->translator->trans('competition.error.already_finished'));
        }

        $competition->updateStartedAt($command->date);

        $fixtures = new FixturesGenerator()->execute($competition);

        $this->repository->save($competition);

        foreach ($fixtures as $fixture) {
            $fixtureToSave = new KeyforgeCompetitionFixture(
                Uuid::v4(),
                $competition->id,
                null,
                [],
                $fixture->reference,
                $fixture->players,
                $fixture->type,
                $fixture->position,
                $fixture->createdAt,
                $fixture->playedAt,
            );

            $this->repository->saveFixture($fixtureToSave);
        }
    }
}
