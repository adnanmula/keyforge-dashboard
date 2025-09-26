<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetGameLogQuery
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
