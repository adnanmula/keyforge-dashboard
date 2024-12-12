<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class FinishCompetitionCommand
{
    private(set) Uuid $competitionId;
    private(set) Uuid $winnerId;
    private(set) \DateTimeImmutable $date;

    public function __construct($competitionId, $winnerId, $date)
    {
        Assert::lazy()
            ->that($competitionId, 'competitionId')->uuid()
            ->that($winnerId, 'winnerId')->uuid()
            ->that($date, 'date')->date('Y-m-d')
            ->verifyNow();

        $this->competitionId = Uuid::from($competitionId);
        $this->winnerId = Uuid::from($winnerId);
        $this->date = new \DateTimeImmutable($date);
    }
}
