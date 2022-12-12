<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Gwent\Game\Create;

use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentCoin;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class CreateGameCommand
{
    private Uuid $userId;
    private Uuid $userDeck;
    private ?Uuid $opponentDeckArchetype;
    private bool $win;
    private ?int $rank;
    private GwentCoin $coin;
    private GwentGameScore $score;
    private \DateTimeImmutable $date;

    public function __construct($userId, $userDeck, $opponentDeckArchetype, $win, $rank, $coin, $playerScore, $opponentScore, $date)
    {
        Assert::lazy()
            ->that($userId, 'userId')->uuid()
            ->that($userDeck, 'userDeck')->uuid()
            ->that($opponentDeckArchetype, 'opponentDeckArchetype')->nullOr()->uuid()
            ->that($win, 'win')->boolean()
            ->that($rank, 'rank')->integerish()->between(0, 30)
            ->that($coin, 'coin')->string()->inArray([GwentCoin::BLUE->name, GwentCoin::RED->name])
            ->that($playerScore, 'playerScore')->integerish()->min(0)->max(2)
            ->that($opponentScore, 'opponentScore')->integerish()->min(0)->max(2)
            ->that($date, 'date')->date('Y-m-d H:i:s');


        $this->userId = $userId;
        $this->userDeck = Uuid::from($userDeck);
        $this->opponentDeckArchetype = null === $opponentDeckArchetype ? null : Uuid::from($opponentDeckArchetype);
        $this->win = $win;
        $this->rank = $rank;
        $this->coin = GwentCoin::from($coin);
        $this->score = GwentGameScore::from($playerScore, $opponentScore);
        $this->date = $date;
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }

    public function userDeck(): Uuid
    {
        return $this->userDeck;
    }

    public function opponentDeckArchetype(): ?Uuid
    {
        return $this->opponentDeckArchetype;
    }

    public function win(): bool
    {
        return $this->win;
    }

    public function rank(): ?int
    {
        return $this->rank;
    }

    public function coin(): GwentCoin
    {
        return $this->coin;
    }

    public function score(): GwentGameScore
    {
        return $this->score;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }
}
