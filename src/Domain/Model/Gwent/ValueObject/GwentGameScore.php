<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent\ValueObject;

final class GwentGameScore implements \JsonSerializable
{
    private function __construct(
        private int $playerScore,
        private int $opponentScore,
    ) {
        $this->assert($playerScore, $opponentScore);
    }

    public static function from(int $playerScore, int $opponentScore): self
    {
        return new self($playerScore, $opponentScore);
    }

    public function playerScore(): int
    {
        return $this->playerScore;
    }

    public function opponentScore(): int
    {
        return $this->opponentScore;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'player_score' => $this->playerScore,
            'opponent_score' => $this->opponentScore,
        ];
    }

    private function assert(int $playerScore, int $opponentScore): void
    {
        if (($playerScore !== 2 || $opponentScore !== 2)
            && $playerScore >= 0 && $playerScore <= 2
            && $opponentScore >= 0 && $opponentScore <= 2
        ) {
            throw new \InvalidArgumentException('Invalid game score');
        }
    }
}
