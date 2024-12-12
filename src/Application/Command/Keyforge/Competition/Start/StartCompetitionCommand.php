<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Start;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class StartCompetitionCommand
{
    private(set) Uuid $competitionId;
    private(set) \DateTimeImmutable $date;

    public function __construct($competitionId, $date)
    {
        Assert::lazy()
            ->that($competitionId, 'competitionId')->uuid()
            ->that($date, 'date')->date('Y-m-d')
            ->verifyNow();

        $this->competitionId = Uuid::from($competitionId);
        $this->date = new \DateTimeImmutable($date);
    }
}
