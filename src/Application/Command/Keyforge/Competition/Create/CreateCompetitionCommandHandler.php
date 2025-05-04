<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Classification\Player;
use AdnanMula\Tournament\Classification\User;
use AdnanMula\Tournament\Fixture\Fixtures;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(CreateCompetitionCommand $command): void
    {
        $this->assertType($command);

        $players = [];
        foreach ($command->players as $index => $player) {
            $players[] = new Player($index, new User($player->value(), 'todo'), 0, 0, 0, 0, 0, 0);
        }

        $competition = new KeyforgeCompetition(
            Uuid::v4(),
            $command->name,
            $command->description,
            $command->type,
            $command->admins,
            $command->players,
            new \DateTimeImmutable(),
            null,
            null,
            $command->visibility,
            null,
            new Fixtures($command->fixturesType, $this->translator->trans('competition.round')),
            new Classification(false, ...$players),
        );

        $this->repository->save($competition);
    }

    private function assertType(CreateCompetitionCommand $command): void
    {
        if (false === $command->type->isRoundRobin()) {
            throw new \Exception('Type not supported yet, only Round Robin (1/2) are available');
        }
    }
}
