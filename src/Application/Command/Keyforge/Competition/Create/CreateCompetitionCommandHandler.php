<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Classification\Player;
use AdnanMula\Tournament\User;
use AdnanMula\Tournament\Fixture\Fixtures;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private KeyforgeUserRepository $usersRepository,
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(CreateCompetitionCommand $command): void
    {
        $this->assertType($command);

        $users = $this->users($command);

        $admins = [];
        $players = [];

        foreach ($command->admins as $admin) {
            $admins[] = $users[$admin->value()];
        }

        foreach ($command->players as $player) {
            $players[] = $users[$player->value()];
        }

        $classificationPlayers = [];
        foreach ($command->players as $index => $classificationPlayer) {
            $classificationPlayers[] = new Player($index, $users[$classificationPlayer->value()], 0, 0, 0, 0, 0, 0);
        }

        $competition = new KeyforgeCompetition(
            Uuid::v4(),
            $command->name,
            $command->description,
            $command->type,
            $admins,
            $players,
            new \DateTimeImmutable(),
            null,
            null,
            $command->visibility,
            null,
            new Fixtures($command->fixturesType, $this->translator->trans('competition.round')),
            new Classification(false, ...$classificationPlayers),
        );

        $this->repository->save($competition);
    }

    private function assertType(CreateCompetitionCommand $command): void
    {
        if (false === $command->type->isRoundRobin()) {
            throw new \Exception('Type not supported yet, only Round Robin (1/2) are available');
        }
    }

    /** @return array<string, User> */
    private function users(CreateCompetitionCommand $command): array
    {
        $ids = \array_unique(\array_merge(
            \array_map(static fn (Uuid $id): string => $id->value(), $command->admins),
            \array_map(static fn (Uuid $id): string => $id->value(), $command->players),
        ));

        $users = $this->usersRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$ids), FilterOperator::IN),
            ),
        ));

        $usersIndexed = [];

        foreach ($users as $user) {
            $usersIndexed[$user->id()->value()] = new User($user->id()->value(), $user->name());
        }

        return $usersIndexed;
    }
}
