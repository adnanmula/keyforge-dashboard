<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Remove;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class RemoveTagCommand
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
