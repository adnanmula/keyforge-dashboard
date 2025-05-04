<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Tournament\Fixture\FixtureType;
use AdnanMula\Tournament\TournamentType;
use Assert\Assert;

final readonly class CreateCompetitionCommand
{
    private(set) string $name;
    private(set) TournamentType $type;
    private(set) FixtureType $fixturesType;
    private(set) array $admins;
    private(set) array $players;
    private(set) string $description;
    private(set) CompetitionVisibility $visibility;

    public function __construct($name, $type, $fixturesType, $admins, $players, $description, $visibility)
    {
        $competitionTypes = [
            TournamentType::ROUND_ROBIN_1->value,
            TournamentType::ROUND_ROBIN_2->value,
            TournamentType::ROUND_ROBIN_3->value,
            TournamentType::ROUND_ROBIN_4->value,
        ];

        $fixtureTypes = [
            FixtureType::BEST_OF_1->value,
            FixtureType::BEST_OF_3->value,
            FixtureType::BEST_OF_5->value,
            FixtureType::BEST_OF_7->value,
            FixtureType::GAMES_3->value,
            FixtureType::GAMES_5->value,
        ];

        Assert::lazy()
            ->that($name, 'name')->string()->notBlank()
            ->that($type, 'type')->inArray($competitionTypes)
            ->that($fixturesType, 'type')->inArray($fixtureTypes)
            ->that($admins, 'admins')->all()->uuid()
            ->that($players, 'players')->all()->uuid()
            ->that($description, 'description')->string()
            ->that($visibility, 'visibility')->inArray(CompetitionVisibility::values())
            ->verifyNow();

        $this->name = $name;
        $this->type = TournamentType::from($type);
        $this->fixturesType = FixtureType::from($fixturesType);
        $this->admins = Uuid::fromArray(...$admins);
        $this->players = Uuid::fromArray(...$players);
        $this->description = $description;
        $this->visibility = CompetitionVisibility::from($visibility);
    }
}
