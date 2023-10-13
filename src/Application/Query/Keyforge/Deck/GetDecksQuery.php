<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Sorting\Sorting;
use Assert\Assert;

final class GetDecksQuery
{
    public ?int $start;
    public ?int $length;
    public ?string $deck;
    public ?array $sets;
    public ?string $houseFilterType;
    public ?array $houses;
    public ?Sorting $sorting;
    public ?Uuid $deckId;
    public ?Uuid $owner;
    public array $owners;
    public bool $onlyOwned;
    public ?string $tagFilterType;
    public array $tags;
    public array $tagsExcluded;
    public int $maxSas;
    public int $minSas;
    public ?Uuid $onlyFriends;

    public function __construct(
        $start,
        $length,
        ?string $deck,
        ?array $sets,
        ?string $houseFilterType,
        ?array $houses,
        ?Sorting $sorting,
        ?string $deckId = null,
        ?string $owner = null,
        array $owners = [],
        bool $onlyOwned = false,
        ?string $tagFilterType = null,
        array $tags = [],
        array $tagsExcluded = [],
        $maxSas = 150,
        $minSas = 0,
        $onlyFriends = null,
    ) {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($sets, 'set')->nullOr()->all()->inArray(KeyforgeSet::values())
            ->that($houseFilterType, 'houseFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($houses, 'house')->nullOr()->all()->inArray(KeyforgeHouse::values())
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid()
            ->that($owners, 'onlyOwned')->all()->uuid()
            ->that($onlyOwned, 'onlyOwned')->boolean()
            ->that($tagFilterType, 'tagFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($tags, 'tags')->all()->uuid()
            ->that($tagsExcluded, 'tagsExcluded')->all()->uuid()
            ->that($maxSas, 'maxSas')->integerish()
            ->that($minSas, 'minSas')->integerish()
            ->that($onlyFriends, 'onlyFriends')->nullOr()->uuid()
            ->verifyNow();

        $this->start = null === $start ? null : (int) $start;
        $this->length = null === $length ? null : (int) $length;
        $this->deck= $deck;
        $this->sets = $sets;
        $this->houseFilterType = $houseFilterType;
        $this->houses = $houses;
        $this->sorting = $sorting;
        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = null !== $owner ? Uuid::from($owner) : null;
        $this->owners = $owners;
        $this->onlyOwned = $onlyOwned;
        $this->tagFilterType = $tagFilterType;
        $this->tags = \array_map(static fn (string $id): Uuid => Uuid::from($id), $tags);
        $this->tagsExcluded = \array_map(static fn (string $id): Uuid => Uuid::from($id), $tagsExcluded);
        $this->maxSas = (int) $maxSas;
        $this->minSas = (int) $minSas;
        $this->onlyFriends = null === $onlyFriends ? null : Uuid::from($onlyFriends);
    }
}
