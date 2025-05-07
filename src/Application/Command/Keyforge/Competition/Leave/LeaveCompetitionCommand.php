<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Leave;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class LeaveCompetitionCommand
{
    private(set) Uuid $id;

    public function __construct($id)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->verifyNow();

        $this->id = Uuid::from($id);
    }
}
