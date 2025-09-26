<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Analyze;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class AnalyzeGameCommand
{
    private(set) Uuid $id;
    private(set) ?Uuid $gameId;
    private(set) ?string $log;

    public function __construct($id, $gameId, $log)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($gameId, 'gameId')->nullOr()->uuid()
            ->that($log, 'log')->nullOr()->string()->notBlank()
            ->verifyNow();

        $this->id = Uuid::from($id);
        $this->gameId = Uuid::fromNullable($gameId);
        $this->log = $log;
    }
}
