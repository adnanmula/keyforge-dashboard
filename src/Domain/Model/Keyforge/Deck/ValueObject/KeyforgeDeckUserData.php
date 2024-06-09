<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeckUserData implements \JsonSerializable
{
    private function __construct(
        public readonly Uuid $deckId,
        public readonly array $owners,
        public readonly int $wins,
        public readonly int $losses,
        public readonly int $winsVsFriends,
        public readonly int $lossesVsFriends,
        public readonly int $winsVsUsers,
        public readonly int $lossesVsUsers,
        public readonly string $notes,
        public array $tags = [],
    ) {}

    public static function from(Uuid $deckId, array $owners, int $wins, int $losses, int $winsVsFriends, int $lossesVsFriends, int $winsVsUsers, int $lossesVsUsers, string $notes, array $tags = []): self
    {
        return new self($deckId, $owners, $wins, $losses, $winsVsFriends, $lossesVsFriends, $winsVsUsers, $lossesVsUsers, $notes, $tags);
    }

    public function setTags(string ...$tags): void
    {
        $this->tags = $tags;
    }

    public function jsonSerialize(): array
    {
        return [
            'deckId' => $this->deckId->value(),
            'owners' => \array_map(static fn (Uuid $u) => $u->value(), $this->owners),
            'wins' => $this->wins,
            'losses' => $this->losses,
            'wins_vs_friends' => $this->wins,
            'losses_vs_friends' => $this->losses,
            'wins_vs_users' => $this->wins,
            'losses_vs_users' => $this->losses,
            'notes' => $this->notes,
            'tags' => $this->tags,
        ];
    }
}
