<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

class KeyforgeDeckTag implements \JsonSerializable
{
    public function __construct(
        public Uuid $id,
        public LocalizedString $name,
        public TagVisibility $visibility,
        public TagStyle $style,
        public TagType $type,
        public bool $archived,
        public ?Uuid $userId = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->jsonSerialize(),
            'visibility' => $this->visibility->name,
            'style' => $this->style->jsonSerialize(),
            'type' => $this->type->name,
            'archived' => $this->archived,
        ];
    }
}
