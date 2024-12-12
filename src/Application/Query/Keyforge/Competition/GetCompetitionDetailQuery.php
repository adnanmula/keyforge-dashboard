<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use Assert\Assert;

final readonly class GetCompetitionDetailQuery
{
    private(set) string $reference;

    public function __construct($reference)
    {
        Assert::lazy()
            ->that($reference, 'reference')->string()->notBlank()
            ->verifyNow();

        $this->reference = $reference;
    }
}
