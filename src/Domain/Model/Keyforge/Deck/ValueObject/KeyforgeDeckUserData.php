<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeckUserData implements \JsonSerializable
{
    private function __construct(
        private readonly Uuid $deckId,
        private readonly ?Uuid $owner,
        private readonly ?array $owners,
        private int $wins,
        private int $losses,
        private int $winsVsFriends,
        private int $lossesVsFriends,
        private int $winsVsUsers,
        private int $lossesVsUsers,
        private string $notes,
        private array $tags = [],
        private bool $active = true,
    ) {}

    public static function from(
        Uuid $deckId,
        ?Uuid $owner,
        ?array $owners,
        int $wins,
        int $losses,
        int $winsVsFriends,
        int $lossesVsFriends,
        int $winsVsUsers,
        int $lossesVsUsers,
        string $notes,
        array $tags = [],
        bool $active = true,
    ): self {
        return new self($deckId, $owner, $owners, $wins, $losses, $winsVsFriends, $lossesVsFriends, $winsVsUsers, $lossesVsUsers, $notes, $tags, $active);
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }

    public function owner(): ?Uuid
    {
        return $this->owner;
    }

    public function owners(): ?array
    {
        return $this->owners;
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

    public function notes(): string
    {
        return $this->notes;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function active(): bool
    {
        return $this->active;
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

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    public function setTags(string ...$tags): void
    {
        $this->tags = $tags;
    }

    public function jsonSerialize(): array
    {
        return [
            'deckId' => $this->deckId->value(),
            'owner' => $this->owner?->value(),
            'owners' => null === $this->owners
                ? null
                : \array_map(static fn (Uuid $u) => $u->value(), $this->owners),
            'wins' => $this->wins,
            'losses' => $this->losses,
            'wins_vs_friends' => $this->winsVsFriends,
            'losses_vs_friends' => $this->lossesVsFriends,
            'wins_vs_users' => $this->winsVsUsers,
            'losses_vs_users' => $this->lossesVsUsers,
            'notes' => $this->notes,
            'tags' => $this->tags,
            'active' => $this->active,
        ];
    }
}
