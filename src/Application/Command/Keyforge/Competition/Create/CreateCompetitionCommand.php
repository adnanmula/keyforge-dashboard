<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class CreateCompetitionCommand
{
    public string $reference;
    public string $name;
    public CompetitionType $type;
    public CompetitionFixtureType $fixturesType;
    /** @var array<Uuid> $users */
    public array $users;
    public string $description;

    public function __construct($reference, $name, $type, $fixturesType, $users, $description)
    {
        Assert::lazy()
            ->that($reference, 'reference')->string()->notBlank()
            ->that($name, 'name')->string()->notBlank()
            ->that($type, 'type')->inArray(CompetitionType::values())
            ->that($fixturesType, 'type')->inArray(CompetitionFixtureType::values())
            ->that($users, 'users')->all()->uuid()
            ->that($description, 'description')->string()
            ->verifyNow();

        $this->reference = $reference;
        $this->name = $name;
        $this->type = CompetitionType::from($type);
        $this->fixturesType = CompetitionFixtureType::from($fixturesType);
        $this->users = \array_map(static fn (string $id): Uuid => Uuid::from($id), $users);
        $this->description = $description;
    }
}
