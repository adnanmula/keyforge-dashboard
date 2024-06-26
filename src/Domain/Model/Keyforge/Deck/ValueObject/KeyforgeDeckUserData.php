<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeckUserData implements \JsonSerializable
{
    private function __construct(
        private readonly Uuid $deckId,
        private readonly ?Uuid $userId,
        private int $wins,
        private int $losses,
        private int $winsVsFriends,
        private int $lossesVsFriends,
        private int $winsVsUsers,
        private int $lossesVsUsers,
    ) {}

    public static function from(
        Uuid $deckId,
        ?Uuid $userId,
        int $wins,
        int $losses,
        int $winsVsFriends,
        int $lossesVsFriends,
        int $winsVsUsers,
        int $lossesVsUsers,
    ): self {
        return new self($deckId, $userId, $wins, $losses, $winsVsFriends, $lossesVsFriends, $winsVsUsers, $lossesVsUsers);
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }

    public function userId(): ?Uuid
    {
        return $this->userId;
    }

    public function wins(): int
    {
        return $this->wins;
    }

    public function losses(): int
    {
        return $this->losses;
    }

    public function winsVsFriends(): int
    {
        return $this->winsVsFriends;
    }

    public function lossesVsFriends(): int
    {
        return $this->lossesVsFriends;
    }

    public function winsVsUsers(): int
    {
        return $this->winsVsUsers;
    }

    public function lossesVsUsers(): int
    {
        return $this->lossesVsUsers;
    }

    public function setWins(int $wins, int $losses, int $winsVsFriends, int $lossesVsFriends, int $winsVsUsers, int $lossesVsUsers): void
    {
        $this->wins = $wins;
        $this->losses = $losses;
        $this->winsVsFriends = $winsVsFriends;
        $this->lossesVsFriends = $lossesVsFriends;
        $this->winsVsUsers = $winsVsUsers;
        $this->lossesVsUsers = $lossesVsUsers;
    }

    public function jsonSerialize(): array
    {
        return [
            'deckId' => $this->deckId->value(),
            'userId' => $this->userId?->value(),
            'wins' => $this->wins,
            'losses' => $this->losses,
            'wins_vs_friends' => $this->winsVsFriends,
            'losses_vs_friends' => $this->lossesVsFriends,
            'wins_vs_users' => $this->winsVsUsers,
            'losses_vs_users' => $this->lossesVsUsers,
        ];
    }
}
