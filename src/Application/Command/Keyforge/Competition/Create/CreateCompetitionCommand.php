<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class CreateCompetitionCommand
{
    private(set) string $name;
    private(set) CompetitionType $type;
    private(set) CompetitionFixtureType $fixturesType;
    private(set) array $admins;
    private(set) string $description;
    private(set) CompetitionVisibility $visibility;

    public function __construct($name, $type, $fixturesType, $admins, $description, $visibility)
    {
        Assert::lazy()
            ->that($name, 'name')->string()->notBlank()
            ->that($type, 'type')->inArray(CompetitionType::values())
            ->that($fixturesType, 'type')->inArray(CompetitionFixtureType::values())
            ->that($admins, 'type')->all()->uuid()
            ->that($description, 'description')->string()
            ->that($visibility, 'visibility')->inArray(CompetitionVisibility::values())
            ->verifyNow();

        $this->name = $name;
        $this->type = CompetitionType::from($type);
        $this->fixturesType = CompetitionFixtureType::from($fixturesType);
        $this->admins = Uuid::fromArray(...$admins);
        $this->description = $description;
        $this->visibility = CompetitionVisibility::from($visibility);
    }
}
