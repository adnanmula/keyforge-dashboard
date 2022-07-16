<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

final class KeyforgeGameScore implements \JsonSerializable
{
    private function __construct(
        private int $winnerScore,
        private int $loserScore,
    ) {
        $this->assert($winnerScore, $loserScore);
    }

    public static function from(int $winnerScore, int $loserScore): self
    {
        return new self($winnerScore, $loserScore);
    }

    public function winnerScore(): int
    {
        return $this->winnerScore;
    }

    public function loserScore(): int
    {
        return $this->loserScore;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'winner_score' => $this->winnerScore,
            'loser_score' => $this->loserScore,
        ];
    }

    private function assert(int $winnerScore, int $loserScore): void
    {
        if ($winnerScore !== 3 || $loserScore < 0 || $loserScore > 2) {
            throw new \InvalidArgumentException('Invalid game score');
        }
    }
}
