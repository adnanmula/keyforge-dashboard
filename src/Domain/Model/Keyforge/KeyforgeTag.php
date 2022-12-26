<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTag implements \JsonSerializable
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public TagVisibility $visibility,
        public TagStyle $style,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function visibility(): TagVisibility
    {
        return $this->visibility;
    }

    public function style(): TagStyle
    {
        return $this->style;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name,
            'visibility' => $this->visibility->name,
            'style' => $this->style->jsonSerialize(),
        ];
    }
}
